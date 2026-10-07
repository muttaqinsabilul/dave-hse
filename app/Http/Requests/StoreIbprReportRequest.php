<?php

namespace App\Http\Requests;

use App\Services\RiskLevelResolver;
use App\Support\SessionAuth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreIbprReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return app(SessionAuth::class)->kind() === SessionAuth::KIND_HSE;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'site_code' => ['required', 'size:2', 'exists:sites,code'],
            'kegiatan' => ['required', 'string'],
            'bahaya' => ['required', 'string'],
            'risiko' => ['required', 'string'],
            'likelihood' => ['required', 'integer', 'min:1', 'max:5'],
            'severity' => ['required', 'integer', 'min:1', 'max:5'],
            'pengendalian' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['site_code', 'likelihood', 'severity', 'pengendalian'])) {
                    return;
                }

                $auth = app(SessionAuth::class);
                $siteCode = $this->string('site_code')->toString();
                $user = $auth->hseUser();

                if ($user === null || ! $user->canAccessSite($siteCode)) {
                    $validator->errors()->add('site_code', 'Di luar wilayah kerja Anda.');
                }

                $resolved = app(RiskLevelResolver::class)->resolve(
                    $this->integer('likelihood'),
                    $this->integer('severity')
                );

                if (in_array($resolved['level'], ['HIGH', 'EXTREME'], true) && trim((string) $this->input('pengendalian')) === '') {
                    $validator->errors()->add('pengendalian', 'Wajib diisi untuk level HIGH/EXTREME.');
                }
            },
        ];
    }
}
