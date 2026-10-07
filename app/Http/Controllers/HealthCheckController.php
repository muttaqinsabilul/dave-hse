<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHealthCheckRequest;
use App\Models\HealthCheck;
use App\Models\Site;
use App\Models\Worker;
use App\Services\HealthStatusResolver;
use App\Support\SessionAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class HealthCheckController extends Controller
{
    public function create(Request $request, SessionAuth $auth): View
    {
        $site = $auth->effectiveSite();

        $workers = Worker::query()
            ->when($site !== null, fn ($q) => $q->forSite($site))
            ->orderBy('id')
            ->get(['id', 'nama', 'site_code']);

        return view('tensi.create', [
            'workers' => $workers,
            'sites' => Site::orderBy('code')->get(),
            'site' => $site,
            'selected' => $request->string('worker_id', '')->toString(),
        ]);
    }

    public function store(StoreHealthCheckRequest $request, SessionAuth $auth, HealthStatusResolver $status): RedirectResponse
    {
        $data = $request->validated();
        $worker = Worker::findOrFail($data['worker_id']);

        abort_unless($auth->hseUser()?->canAccessSite($worker->site_code) ?? false, Response::HTTP_FORBIDDEN);

        $check = HealthCheck::updateOrCreate(
            ['worker_id' => $worker->id, 'tanggal' => today()],
            [
                'sistol' => $data['sistol'],
                'diastol' => $data['diastol'],
                'suhu' => $data['suhu'],
                'inspector_id' => $auth->hseUser()->id,
                'status' => $status->resolve($data['sistol'], $data['diastol'], (float) $data['suhu']),
            ]
        );

        return redirect()->route('dashboard')->with('ok', "Tensi {$worker->nama} tersimpan: {$check->status}.");
    }
}
