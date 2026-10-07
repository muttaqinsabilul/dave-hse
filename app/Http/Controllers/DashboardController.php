<?php

namespace App\Http\Controllers;

use App\Models\HealthCheck;
use App\Models\IbprReport;
use App\Models\Site;
use App\Models\Worker;
use App\Support\SessionAuth;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, SessionAuth $auth): View
    {
        $user = $auth->hseUser();
        $ownWorker = $auth->worker();

        $requestedSite = $request->string('site', '')->toString();
        $requestedSite = $requestedSite !== '' && Site::where('code', $requestedSite)->exists() ? $requestedSite : null;
        $site = $auth->effectiveSite($requestedSite);

        $from = $request->string('from', '')->toString();
        $to = $request->string('to', '')->toString();
        $hasDateFilter = $from !== '' || $to !== '';

        $workersQuery = Worker::query()->with(['site', 'healthChecks' => fn ($q) => $q->today()]);

        $ibprQuery = IbprReport::query()
            ->when($from !== '', fn ($q) => $q->whereDate('tanggal_realtime', '>=', $from))
            ->when($to !== '', fn ($q) => $q->whereDate('tanggal_realtime', '<=', $to))
            ->when(! $hasDateFilter, fn ($q) => $q->recent(30));

        $checksQuery = HealthCheck::query()
            ->when($from !== '', fn ($q) => $q->whereDate('tanggal', '>=', $from))
            ->when($to !== '', fn ($q) => $q->whereDate('tanggal', '<=', $to))
            ->when(! $hasDateFilter, fn ($q) => $q->today());

        if ($site !== null) {
            $workersQuery->forSite($site);
            $ibprQuery->forSite($site);
            $checksQuery->forSite($site);
        }

        if ($ownWorker !== null) {
            $workersQuery->where('id', $ownWorker->id);
        }

        if ($search = trim($request->string('q', '')->toString())) {
            $workersQuery->search($search);
        }

        $baseWorkers = Worker::query();
        if ($site !== null) {
            $baseWorkers->forSite($site);
        }
        if ($ownWorker !== null) {
            $baseWorkers->where('id', $ownWorker->id);
        }

        $stats = [
            'pekerja' => (clone $baseWorkers)->count(),
            'pekerja_onsite' => (clone $baseWorkers)->onsite()->count(),
            'pekerja_offsite' => (clone $baseWorkers)->offsite()->count(),
            'ibpr_berisiko' => (clone $ibprQuery)->highRisk()->count(),
            'flag_hari_ini' => (clone $checksQuery)->flagged()->count(),
        ];

        $perSite = Worker::query()
            ->selectRaw('site_code, COUNT(*) as total')
            ->when($site !== null, fn ($q) => $q->where('site_code', $site))
            ->groupBy('site_code')
            ->pluck('total', 'site_code');

        $donut = IbprReport::query()
            ->selectRaw('level, COUNT(*) as total')
            ->when($site !== null, fn ($q) => $q->forSite($site))
            ->when($from !== '', fn ($q) => $q->whereDate('tanggal_realtime', '>=', $from))
            ->when($to !== '', fn ($q) => $q->whereDate('tanggal_realtime', '<=', $to))
            ->when(! $hasDateFilter, fn ($q) => $q->recent(30))
            ->groupBy('level')
            ->pluck('total', 'level');

        $trendRows = HealthCheck::query()
            ->selectRaw('tanggal, status, COUNT(*) as total')
            ->where('tanggal', '>=', today()->subDays(6))
            ->when($site !== null, fn ($q) => $q->forSite($site))
            ->groupBy('tanggal', 'status')
            ->get();

        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i)->toDateString();
            $trend[] = [
                'label' => today()->subDays($i)->locale('id')->isoFormat('ddd'),
                'normal' => (int) $trendRows->where('tanggal', $date)->where('status', 'NORMAL')->sum('total'),
                'flag' => (int) $trendRows->where('tanggal', $date)->where('status', 'FLAG')->sum('total'),
            ];
        }

        $ibprListQuery = IbprReport::query()
            ->with(['site', 'inspector'])
            ->when($site !== null, fn ($q) => $q->forSite($site))
            ->when($from !== '', fn ($q) => $q->whereDate('tanggal_realtime', '>=', $from))
            ->when($to !== '', fn ($q) => $q->whereDate('tanggal_realtime', '<=', $to))
            ->when($request->filled('level'), fn ($q) => $q->where('level', $request->string('level')->toString()))
            ->latest('tanggal_realtime');

        $totalIbpr = (clone $ibprListQuery)->count();
        $ibprList = $ibprListQuery
            ->simplePaginate(5, ['*'], 'pi')
            ->withQueryString();

        $totalWorkers = (clone $workersQuery)->count();
        $workers = $workersQuery
            ->orderBy('id')
            ->simplePaginate(5, ['*'], 'pw')
            ->withQueryString();

        return view('dashboard', [
            'user' => $user,
            'ownWorker' => $ownWorker,
            'sites' => Site::orderBy('code')->get(),
            'site' => $site,
            'stats' => $stats,
            'perSite' => $perSite,
            'donut' => $donut,
            'trend' => $trend,
            'workers' => $workers,
            'totalWorkers' => $totalWorkers,
            'ibprList' => $ibprList,
            'totalIbpr' => $totalIbpr,
            'search' => $search ?? '',
            'levelFilter' => $request->string('level', '')->toString(),
            'from' => $from,
            'to' => $to,
            'hasDateFilter' => $hasDateFilter,
        ]);
    }
}
