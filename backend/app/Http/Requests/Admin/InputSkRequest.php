<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InputSkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'NOMER_SK' => 'required|string|max:50',
            'TANGGAL_SK' => 'required|date',
        ];
    }
}
