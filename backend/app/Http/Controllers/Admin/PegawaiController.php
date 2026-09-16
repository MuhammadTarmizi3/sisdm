<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePegawaiRequest;
use App\Http\Requests\Admin\UpdatePegawaiRequest;
use App\Http\Resources\PegawaiResource;
use App\Models\Pegawai;

class PegawaiController extends Controller
{
    public function index()
    {
        return response()->json(['data' => PegawaiResource::collection(Pegawai::all())]);
    }

    public function store(StorePegawaiRequest $request)
    {
        $pegawai = Pegawai::create($request->validated());
        return response()->json(['message' => 'Pegawai berhasil ditambahkan', 'data' => new PegawaiResource($pegawai)], 201);
    }

    public function show(Pegawai $pegawai)
    {
        return response()->json(['data' => new PegawaiResource($pegawai)]);
    }

    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai)
    {
        $pegawai->update($request->validated());
        return response()->json(['message' => 'Pegawai berhasil diupdate', 'data' => new PegawaiResource($pegawai)]);
    }

    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();
        return response()->json(['message' => 'Pegawai berhasil dihapus']);
    }
}
