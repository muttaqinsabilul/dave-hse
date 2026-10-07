<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkerRequest;
use App\Models\Site;
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

        $workers = Worker::query()
            ->with(['site', 'healthChecks' => fn ($q) => $q->today()])
            ->when($site !== null, fn ($q) => $q->forSite($site))
            ->when($request->filled('q'), fn ($q) => $q->search(trim($request->string('q')->toString())))
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('pekerja.index', [
            'workers' => $workers,
            'sites' => Site::orderBy('code')->get(),
            'site' => $site,
        ]);
    }

    public function create(): View
    {
        return view('pekerja.create', [
            'sites' => Site::orderBy('code')->get(),
            'mandorPerSite' => config('hse.mandor_per_site'),
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
