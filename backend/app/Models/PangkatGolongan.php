<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PangkatGolongan extends Model
{
    protected $table = 'pangkat_golongan';
    protected $primaryKey = 'ID_PANGKAT';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'ID_PANGKAT', 'ID_PANGKAT');
    }

    public function riwayatPangkat()
    {
        return $this->hasMany(RiwayatPangkat::class, 'ID_PANGKAT', 'ID_PANGKAT');
    }
}
