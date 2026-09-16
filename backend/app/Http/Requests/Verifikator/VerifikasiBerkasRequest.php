<?php

namespace App\Http\Requests\Verifikator;

use Illuminate\Foundation\Http\FormRequest;

class VerifikasiBerkasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'STATUS_VERIFIKASI' => 'required|in:valid,tidak valid,perlu perbaikan',
            'CATATAN' => 'nullable|string',
        ];
    }
}
