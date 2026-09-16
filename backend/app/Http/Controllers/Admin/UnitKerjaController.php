<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMasterRequest;
use App\Models\UnitKerja;

class UnitKerjaController extends Controller
{
    public function index()
    {
        return response()->json(['data' => UnitKerja::all()]);
    }

    public function store(StoreMasterRequest $request)
    {
        $unit = UnitKerja::create(['NAMA_UNIT' => $request->NAMA]);
        return response()->json(['message' => 'Unit Kerja berhasil ditambahkan', 'data' => $unit], 201);
    }

    public function show($id)
    {
        return response()->json(['data' => UnitKerja::findOrFail($id)]);
    }

    public function update(StoreMasterRequest $request, $id)
    {
        $unit = UnitKerja::findOrFail($id);
        $unit->update(['NAMA_UNIT' => $request->NAMA]);
        return response()->json(['message' => 'Unit Kerja berhasil diupdate', 'data' => $unit]);
    }

    public function destroy($id)
    {
        UnitKerja::findOrFail($id)->delete();
        return response()->json(['message' => 'Unit Kerja berhasil dihapus']);
    }
}
