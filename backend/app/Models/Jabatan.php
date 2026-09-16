<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $primaryKey = 'ID_JABATAN';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'ID_JABATAN', 'ID_JABATAN');
    }

    public function riwayatJabatan()
    {
        return $this->hasMany(RiwayatJabatan::class, 'ID_JABATAN', 'ID_JABATAN');
    }
}
