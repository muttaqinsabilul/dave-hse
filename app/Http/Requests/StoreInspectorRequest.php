<?php

namespace App\Http\Requests;

use App\Support\SessionAuth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInspectorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return app(SessionAuth::class)->hseUser()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'max:20', 'regex:'.config('hse.id_patterns.inspector'), Rule::unique('users', 'id')],
            'site_code' => ['required', 'size:2', 'exists:sites,code'],
            'name' => ['required', 'string', 'max:100'],
        ];
    }
}
