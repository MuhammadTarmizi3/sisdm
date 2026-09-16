<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAkunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'USERNAME' => 'nullable|string|max:50|unique:users,USERNAME,' . $this->route('akun') . ',USER_ID',
            'PASSWORD' => 'nullable|string|min:8',
            'ID_ROLE' => 'nullable|integer|exists:roles,id',
            'ID_PEGAWAI' => 'nullable|integer|exists:pegawai,ID_PEGAWAI',
        ];
    }
}
