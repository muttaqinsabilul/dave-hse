<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInspectorRequest;
use App\Models\Site;
use App\Models\User;
use App\Support\SessionAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class InspectorController extends Controller
{
    public function index(SessionAuth $auth): View
    {
        abort_unless($auth->hseUser()?->isAdmin() ?? false, Response::HTTP_FORBIDDEN);

        return view('inspector.index', [
            'inspectors' => User::inspectors()->with('site')->orderBy('id')->paginate(15),
        ]);
    }

    public function create(SessionAuth $auth): View
    {
        abort_unless($auth->hseUser()?->isAdmin() ?? false, Response::HTTP_FORBIDDEN);

        return view('inspector.create', ['sites' => Site::orderBy('code')->get()]);
    }

    public function store(StoreInspectorRequest $request): RedirectResponse
    {
        User::create([...$request->validated(), 'role' => 'inspector', 'pin' => null]);

        return redirect()->route('inspector.index')->with('ok', 'Akun inspector dibuat.');
    }
}
