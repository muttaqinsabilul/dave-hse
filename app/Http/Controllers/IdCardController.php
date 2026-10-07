<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use App\Support\SessionAuth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class IdCardController extends Controller
{
    public function show(Worker $worker, SessionAuth $auth): View
    {
        $user = $auth->hseUser();
        $own = $auth->worker();

        if ($user !== null && ! $user->canAccessSite($worker->site_code)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if ($own !== null && $own->id !== $worker->id) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $worker->load('site');

        return view('idcard.show', ['worker' => $worker]);
    }
}
