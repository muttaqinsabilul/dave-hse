<?php

namespace App\Http\Requests;

use App\Support\SessionAuth;
use Illuminate\Foundation\Http\FormRequest;

class StoreHealthCheckRequest extends FormRequest
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
            'worker_id' => ['required', 'string', 'exists:workers,id'],
            'sistol' => ['required', 'integer', 'min:70', 'max:220'],
            'diastol' => ['required', 'integer', 'min:40', 'max:130'],
            'suhu' => ['required', 'numeric', 'min:34', 'max:42'],
        ];
    }
}
