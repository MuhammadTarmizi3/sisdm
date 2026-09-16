<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPangkat extends Model
{
    protected $table = 'riwayat_pangkat';
    protected $primaryKey = 'ID_RIWAYAT_PANGKAT';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'TMT_PANGKAT' => 'date',
        'TANGGAL_SK' => 'date',
        'CREATED_AT' => 'datetime',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function pangkatGolongan()
    {
        return $this->belongsTo(PangkatGolongan::class, 'ID_PANGKAT', 'ID_PANGKAT');
    }
}
