<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkerRequest;
use App\Models\Site;
use App\Models\Subcontractor;
use App\Models\Worker;
use App\Services\WorkerIdGenerator;
use App\Support\SessionAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class WorkerController extends Controller
{
    public function index(Request $request, SessionAuth $auth): View
    {
        abort_unless($auth->kind() === SessionAuth::KIND_HSE, Response::HTTP_FORBIDDEN);

        $site = $auth->effectiveSite($request->string('site', '')->toString() ?: null);

        $statusInput = strtoupper(trim($request->string('status', '')->toString()));
        $status = in_array($statusInput, [Worker::STATUS_ONSITE, Worker::STATUS_OFFSITE], true) ? $statusInput : null;

        $baseQuery = Worker::query()->when($site !== null, fn ($q) => $q->forSite($site));
        $totalCount = (clone $baseQuery)->count();
        $onsiteCount = (clone $baseQuery)->where('status_lokasi', Worker::STATUS_ONSITE)->count();
        $offsiteCount = (clone $baseQuery)->where('status_lokasi', Worker::STATUS_OFFSITE)->count();

        $workers = (clone $baseQuery)
            ->with(['site', 'healthChecks' => fn ($q) => $q->today()])
            ->when($status === Worker::STATUS_ONSITE, fn ($q) => $q->onsite())
            ->when($status === Worker::STATUS_OFFSITE, fn ($q) => $q->offsite())
            ->when($request->filled('q'), fn ($q) => $q->search(trim($request->string('q')->toString())))
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('pekerja.index', [
            'workers' => $workers,
            'sites' => Site::orderBy('code')->get(),
            'site' => $site,
            'status' => $status,
            'totalCount' => $totalCount,
            'onsiteCount' => $onsiteCount,
            'offsiteCount' => $offsiteCount,
            'isAdmin' => $auth->hseUser()?->isAdmin() ?? false,
        ]);
    }

    public function create(): View
    {
        $dbMandor = Subcontractor::all()->groupBy('site_code')->map(fn ($list) => $list->pluck('nama')->all())->toArray();
        $configMandor = config('hse.mandor_per_site', []);
        $mandorPerSite = [];
        foreach (Site::all() as $s) {
            $mandorPerSite[$s->code] = ! empty($dbMandor[$s->code]) ? $dbMandor[$s->code] : ($configMandor[$s->code] ?? []);
        }

        return view('pekerja.create', [
            'sites' => Site::orderBy('code')->get(),
            'mandorPerSite' => $mandorPerSite,
        ]);
    }

    public function store(StoreWorkerRequest $request, WorkerIdGenerator $ids, SessionAuth $auth): RedirectResponse
    {
        $data = $request->safe()->except('foto');

        $worker = Worker::create([
            ...$data,
            'id' => $ids->next($data['site_code']),
            'foto_path' => $request->file('foto')->store('workers', 'public'),
            'tanggal_regis' => today(),
        ]);

        $auth->loginWorker($worker);

        return redirect()->route('idcard.show', $worker);
    }

    public function show(Worker $worker, SessionAuth $auth): View|RedirectResponse
    {
        $user = $auth->hseUser();
        $own = $auth->worker();

        if ($user !== null && ! $user->canAccessSite($worker->site_code)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if ($own !== null && $own->id !== $worker->id) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $worker->load(['site', 'healthChecks' => fn ($q) => $q->latest('tanggal')->limit(7)]);

        return view('pekerja.show', ['worker' => $worker]);
    }

    public function toggleLokasi(Worker $worker, SessionAuth $auth): RedirectResponse
    {
        $user = $auth->hseUser();
        $own = $auth->worker();

        $canToggle = ($user !== null && $user->canAccessSite($worker->site_code))
            || ($own !== null && $own->id === $worker->id);

        abort_unless($canToggle, Response::HTTP_FORBIDDEN);

        $newStatus = $worker->isOnsite() ? Worker::STATUS_OFFSITE : Worker::STATUS_ONSITE;
        $worker->update(['status_lokasi' => $newStatus]);

        $label = $newStatus === Worker::STATUS_ONSITE ? 'On-site' : 'Off-site';

        return back()->with('status', "Status keberadaan {$worker->nama} berhasil diubah menjadi {$label}.");
    }
}
