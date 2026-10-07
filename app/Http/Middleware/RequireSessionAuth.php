<?php

namespace App\Http\Middleware;

use App\Support\SessionAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireSessionAuth
{
    public function __construct(private SessionAuth $auth) {}

    public function handle(Request $request, Closure $next): Response
    {
        $valid = match ($this->auth->kind()) {
            SessionAuth::KIND_HSE => $this->auth->hseUser() !== null,
            SessionAuth::KIND_PEkerja => $this->auth->worker() !== null,
            default => false,
        };

        if (! $valid) {
            $this->auth->logout();

            return redirect()->route('masuk');
        }

        return $next($request);
    }
}
