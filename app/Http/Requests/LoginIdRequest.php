<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginIdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'max:20'],
            'kind' => ['required', 'in:hse,pekerja'],
        ];
    }
}
