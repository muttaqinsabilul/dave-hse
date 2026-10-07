<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
            'mandor_subkon' => ['required', 'string', Rule::in(collect(config('hse.mandor_per_site'))->flatten()->all())],
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

                $allowed = config('hse.mandor_per_site.'.$this->string('site_code')->toString(), []);

                if (! in_array($this->string('mandor_subkon')->toString(), $allowed, true)) {
                    $validator->errors()->add('mandor_subkon', 'Mandor/subkon tidak terdaftar di site ini.');
                }
            },
        ];
    }
}
