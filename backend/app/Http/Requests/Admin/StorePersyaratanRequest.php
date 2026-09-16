<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePersyaratanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'NAMA_PERSYARATAN' => 'required|string|max:150',
            'WAJIB' => 'required|boolean',
            'URUTAN' => 'required|integer',
            'IS_ACTIVE' => 'boolean',
        ];
    }
}
