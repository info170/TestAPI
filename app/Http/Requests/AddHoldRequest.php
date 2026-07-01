<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddHoldRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'idempotency_key' => ['required', 'uuid'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
            'idempotency_key' => $this->header('Idempotency-Key'),
        ]);
    }
}
