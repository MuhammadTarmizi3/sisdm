<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAkunRequest;
use App\Http\Requests\Admin\UpdateAkunRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{
    public function index()
    {
        return response()->json(['data' => UserResource::collection(User::with('roles')->get())]);
    }

    public function store(StoreAkunRequest $request)
    {
        $user = User::create([
            'USERNAME' => $request->USERNAME,
            'PASSWORD' => Hash::make($request->PASSWORD),
            'ID_PEGAWAI' => $request->ID_PEGAWAI,
            'IS_ACTIVE' => true,
        ]);
        
        // Ensure you have Spatie Roles configured correctly mapping ID_ROLE
        // Example assumes Spatie role ID or assignment logic handles ID_ROLE correctly
        $user->assignRole($request->ID_ROLE);

        return response()->json(['message' => 'Akun berhasil dibuat', 'data' => new UserResource($user)], 201);
    }

    public function show(User $akun)
    {
        return response()->json(['data' => new UserResource($akun)]);
    }

    public function update(UpdateAkunRequest $request, User $akun)
    {
        $data = $request->validated();
        if (isset($data['PASSWORD'])) {
            $data['PASSWORD'] = Hash::make($data['PASSWORD']);
        }
        $akun->update($data);

        if (isset($data['ID_ROLE'])) {
            $akun->syncRoles($data['ID_ROLE']);
        }

        return response()->json(['message' => 'Akun berhasil diupdate', 'data' => new UserResource($akun)]);
    }

    public function destroy(User $akun)
    {
        $akun->update(['IS_ACTIVE' => !$akun->IS_ACTIVE]);
        return response()->json(['message' => 'Status akun berhasil diubah']);
    }
}
