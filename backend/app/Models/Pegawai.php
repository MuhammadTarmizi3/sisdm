<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use SoftDeletes;

    protected $table = 'pegawai';
    protected $primaryKey = 'ID_PEGAWAI';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = [];

    protected $casts = [
        'TGL_LAHIR' => 'date',
        'TMT_CPNS' => 'date',
        'TMT_PNS' => 'date',
    ];

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'ID_UNIT', 'ID_UNIT');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'ID_JABATAN', 'ID_JABATAN');
    }

    public function pangkatGolongan()
    {
        return $this->belongsTo(PangkatGolongan::class, 'ID_PANGKAT', 'ID_PANGKAT');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function riwayatJabatan()
    {
        return $this->hasMany(RiwayatJabatan::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function riwayatPangkat()
    {
        return $this->hasMany(RiwayatPangkat::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }
}
