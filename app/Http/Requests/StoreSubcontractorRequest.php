<?php

namespace App\Http\Requests;

use App\Support\SessionAuth;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubcontractorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return app(SessionAuth::class)->hseUser()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'site_code' => ['required', 'string', 'exists:sites,code'],
            'nama' => ['required', 'string', 'max:100'],
            'bidang' => ['nullable', 'string', 'max:100'],
            'kontak' => ['nullable', 'string', 'max:100'],
        ];
    }
}
