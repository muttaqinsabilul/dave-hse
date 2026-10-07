<?php

namespace App\Support;

use App\Models\User;
use App\Models\Worker;

class SessionAuth
{
    public const KEY_ID = 'auth_id';

    public const KEY_KIND = 'auth_kind';

    public const KIND_HSE = 'hse';

    public const KIND_PEkerja = 'pekerja';

    public function loginHse(User $user): void
    {
        session()->put([self::KEY_ID => $user->id, self::KEY_KIND => self::KIND_HSE]);
        session()->regenerate();
    }

    public function loginWorker(Worker $worker): void
    {
        session()->put([self::KEY_ID => $worker->id, self::KEY_KIND => self::KIND_PEkerja]);
        session()->regenerate();
    }

    public function logout(): void
    {
        session()->forget([self::KEY_ID, self::KEY_KIND]);
        session()->regenerate();
    }

    public function kind(): ?string
    {
        $kind = session()->get(self::KEY_KIND);

        return in_array($kind, [self::KIND_HSE, self::KIND_PEkerja], true) ? $kind : null;
    }

    public function hseUser(): ?User
    {
        if ($this->kind() !== self::KIND_HSE) {
            return null;
        }

        return User::find(session()->get(self::KEY_ID));
    }

    public function worker(): ?Worker
    {
        if ($this->kind() !== self::KIND_PEkerja) {
            return null;
        }

        return Worker::find(session()->get(self::KEY_ID));
    }

    /**
     * Site yang boleh diakses sesi ini. Admin boleh pilih (null = semua).
     */
    public function effectiveSite(?string $requested = null): ?string
    {
        $user = $this->hseUser();

        if ($user !== null) {
            return $user->isAdmin() ? $requested : $user->site_code;
        }

        return $this->worker()?->site_code;
    }
}
