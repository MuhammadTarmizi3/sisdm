<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePersyaratanRequest;
use App\Models\PersyaratanMaster;

class PersyaratanKgbController extends Controller
{
    public function index()
    {
        return response()->json(['data' => PersyaratanMaster::all()]);
    }

    public function store(StorePersyaratanRequest $request)
    {
        $persyaratan = PersyaratanMaster::create($request->validated());
        return response()->json(['message' => 'Persyaratan berhasil ditambahkan', 'data' => $persyaratan], 201);
    }

    public function show($id)
    {
        return response()->json(['data' => PersyaratanMaster::findOrFail($id)]);
    }

    public function update(StorePersyaratanRequest $request, $id)
    {
        $persyaratan = PersyaratanMaster::findOrFail($id);
        $persyaratan->update($request->validated());
        return response()->json(['message' => 'Persyaratan berhasil diupdate', 'data' => $persyaratan]);
    }

    public function destroy($id)
    {
        PersyaratanMaster::findOrFail($id)->delete();
        return response()->json(['message' => 'Persyaratan berhasil dihapus']);
    }
}
