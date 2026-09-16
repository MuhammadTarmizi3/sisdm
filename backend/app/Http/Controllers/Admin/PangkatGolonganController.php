<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMasterRequest;
use App\Models\PangkatGolongan;

class PangkatGolonganController extends Controller
{
    public function index()
    {
        return response()->json(['data' => PangkatGolongan::all()]);
    }

    public function store(StoreMasterRequest $request)
    {
        $pangkat = PangkatGolongan::create(['NAMA_PANGKAT' => $request->NAMA]);
        return response()->json(['message' => 'Pangkat Golongan berhasil ditambahkan', 'data' => $pangkat], 201);
    }

    public function show($id)
    {
        return response()->json(['data' => PangkatGolongan::findOrFail($id)]);
    }

    public function update(StoreMasterRequest $request, $id)
    {
        $pangkat = PangkatGolongan::findOrFail($id);
        $pangkat->update(['NAMA_PANGKAT' => $request->NAMA]);
        return response()->json(['message' => 'Pangkat Golongan berhasil diupdate', 'data' => $pangkat]);
    }

    public function destroy($id)
    {
        PangkatGolongan::findOrFail($id)->delete();
        return response()->json(['message' => 'Pangkat Golongan berhasil dihapus']);
    }
}
