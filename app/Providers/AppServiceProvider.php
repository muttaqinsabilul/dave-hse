<?php

namespace App\Providers;

use App\Models\Worker;
use App\Support\SessionAuth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view): void {
            $auth = app(SessionAuth::class);
            $user = $auth->hseUser();
            $worker = $auth->worker();

            $authName = null;
            $authRole = null;
            if ($user !== null) {
                $authName = $user->name ?: ($user->isAdmin() ? 'Super Admin' : "Inspector {$user->id}");
                $authRole = $user->isAdmin() ? 'Super Administrator' : "Inspector Site {$user->site_code}";
            } elseif ($worker !== null) {
                $authName = $worker->nama ?: "Pekerja {$worker->id}";
                $authRole = "Pekerja • Site {$worker->site_code}";
            }

            $navTotalPekerja = null;
            if ($user !== null) {
                $navTotalPekerja = Worker::query()
                    ->when(! $user->isAdmin() && $user->site_code, fn ($q) => $q->where('site_code', $user->site_code))
                    ->count();
            }

            $view->with([
                'kind' => $auth->kind(),
                'isAdmin' => $user?->isAdmin() ?? false,
                'authUser' => $user,
                'authWorker' => $worker,
                'authName' => $authName,
                'authRole' => $authRole,
                'authLabel' => $authName,
                'navTotalPekerja' => $navTotalPekerja,
            ]);
        });
    }
}
