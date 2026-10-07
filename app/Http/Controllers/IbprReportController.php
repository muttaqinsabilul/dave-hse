<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIbprReportRequest;
use App\Models\IbprReport;
use App\Models\Site;
use App\Services\RiskLevelResolver;
use App\Support\SessionAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IbprReportController extends Controller
{
    public function index(Request $request, SessionAuth $auth): View
    {
        $site = $auth->effectiveSite($request->string('site', '')->toString() ?: null);
        $from = $request->string('from', '')->toString();
        $to = $request->string('to', '')->toString();
        $search = trim($request->string('q', '')->toString());
        $levelFilter = $request->string('level', '')->toString();

        $reports = IbprReport::query()
            ->with(['site', 'inspector'])
            ->when($site !== null, fn ($q) => $q->forSite($site))
            ->when($from !== '', fn ($q) => $q->whereDate('tanggal_realtime', '>=', $from))
            ->when($to !== '', fn ($q) => $q->whereDate('tanggal_realtime', '<=', $to))
            ->when($levelFilter !== '', fn ($q) => $q->where('level', $levelFilter))
            ->when($search !== '', fn ($q) => $q->where(fn ($sub) => $sub->where('kegiatan', 'like', "%{$search}%")->orWhere('bahaya', 'like', "%{$search}%")->orWhere('risiko', 'like', "%{$search}%")))
            ->latest('tanggal_realtime')
            ->paginate(15)
            ->withQueryString();

        return view('ibpr.index', [
            'reports' => $reports,
            'sites' => Site::orderBy('code')->get(),
            'site' => $site,
            'from' => $from,
            'to' => $to,
            'search' => $search,
            'levelFilter' => $levelFilter,
            'isAdmin' => $auth->hseUser()?->isAdmin() ?? false,
        ]);
    }

    public function create(SessionAuth $auth): View
    {
        return view('ibpr.create', [
            'sites' => Site::orderBy('code')->get(),
            'lockedSite' => $auth->effectiveSite(),
            'isAdmin' => $auth->hseUser()?->isAdmin() ?? false,
        ]);
    }

    public function store(StoreIbprReportRequest $request, SessionAuth $auth, RiskLevelResolver $risk): RedirectResponse
    {
        $data = $request->validated();
        $resolved = $risk->resolve($data['likelihood'], $data['severity']);

        IbprReport::create([
            ...$data,
            'tanggal_realtime' => now(),
            'skor' => $resolved['skor'],
            'level' => $resolved['level'],
            'inspector_id' => $auth->hseUser()->id,
        ]);

        return redirect()->route('dashboard')->with('ok', "IBPR tersimpan: {$resolved['level']} ({$resolved['skor']}).");
    }
}
