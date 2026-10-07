<?php

namespace App\Http\Requests;

use App\Models\Subcontractor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreWorkerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'site_code' => ['required', 'size:2', 'exists:sites,code'],
            'nama' => ['required', 'string', 'max:100'],
            'jenis_pekerjaan' => ['required', 'string', 'max:100'],
            'mandor_subkon' => ['required', 'string', 'max:100'],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'usia' => ['required', 'integer', 'min:17', 'max:65'],
            'asal' => ['required', 'string', 'max:100'],
            'riwayat_penyakit' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['site_code', 'mandor_subkon'])) {
                    return;
                }

                $siteCode = $this->string('site_code')->toString();
                $subkonInput = $this->string('mandor_subkon')->toString();

                $dbAllowed = Subcontractor::where('site_code', $siteCode)->pluck('nama')->all();
                $configAllowed = config('hse.mandor_per_site.'.$siteCode, []);
                $allowed = array_unique(array_merge($dbAllowed, $configAllowed));

                if (! in_array($subkonInput, $allowed, true)) {
                    $validator->errors()->add('mandor_subkon', 'Mandor/subkon tidak terdaftar di site ini.');
                }
            },
        ];
    }
}
