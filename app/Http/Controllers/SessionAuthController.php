<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginIdRequest;
use App\Models\User;
use App\Models\Worker;
use App\Support\SessionAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SessionAuthController extends Controller
{
    public function pilihan(SessionAuth $auth): RedirectResponse|View
    {
        if ($auth->kind() !== null) {
            return redirect()->route('dashboard');
        }

        return view('masuk');
    }

    public function login(LoginIdRequest $request, SessionAuth $auth): RedirectResponse
    {
        $id = $request->string('id')->toString();

        if ($request->string('kind')->toString() === SessionAuth::KIND_HSE) {
            $user = User::find($id);

            if ($user === null) {
                return back()->withErrors(['id' => 'ID HSE tidak terdaftar.'])->withInput();
            }

            $auth->loginHse($user);
        } else {
            $worker = Worker::find($id);

            if ($worker === null) {
                return back()->withErrors(['id' => 'ID Pekerja tidak terdaftar. Daftar dulu bila belum punya.'])->withInput();
            }

            $auth->loginWorker($worker);
        }

        return redirect()->route('dashboard');
    }

    public function logout(SessionAuth $auth): RedirectResponse
    {
        $auth->logout();

        return redirect()->route('masuk');
    }
}
