<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAkunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'USERNAME' => 'required|string|max:50|unique:users,USERNAME',
            'PASSWORD' => 'required|string|min:8',
            'ID_ROLE' => 'required|integer|exists:roles,id', // Spatie uses 'id' usually, but adjust to match your db structure if needed. Assuming roles table uses id
            'ID_PEGAWAI' => 'nullable|integer|exists:pegawai,ID_PEGAWAI',
        ];
    }
}
