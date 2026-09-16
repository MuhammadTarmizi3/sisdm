<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMasterRequest;
use App\Models\Jabatan;

class JabatanController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Jabatan::all()]);
    }

    public function store(StoreMasterRequest $request)
    {
        $jabatan = Jabatan::create(['NAMA_JABATAN' => $request->NAMA]);
        return response()->json(['message' => 'Jabatan berhasil ditambahkan', 'data' => $jabatan], 201);
    }

    public function show($id)
    {
        return response()->json(['data' => Jabatan::findOrFail($id)]);
    }

    public function update(StoreMasterRequest $request, $id)
    {
        $jabatan = Jabatan::findOrFail($id);
        $jabatan->update(['NAMA_JABATAN' => $request->NAMA]);
        return response()->json(['message' => 'Jabatan berhasil diupdate', 'data' => $jabatan]);
    }

    public function destroy($id)
    {
        Jabatan::findOrFail($id)->delete();
        return response()->json(['message' => 'Jabatan berhasil dihapus']);
    }
}
