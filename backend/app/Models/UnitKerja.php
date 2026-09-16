<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    protected $table = 'unit_kerja';
    protected $primaryKey = 'ID_UNIT';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'ID_UNIT', 'ID_UNIT');
    }
}
