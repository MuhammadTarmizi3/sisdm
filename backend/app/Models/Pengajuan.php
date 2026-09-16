<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';
    protected $primaryKey = 'ID_PENGAJUAN';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = [];

    protected $casts = [
        'TANGGAL_PENGAJUAN' => 'date',
        'TANGGAL_SK' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function jenisLayanan()
    {
        return $this->belongsTo(JenisLayanan::class, 'ID_LAYANAN', 'ID_LAYANAN');
    }

    public function detailBerkas()
    {
        return $this->hasMany(DetailBerkas::class, 'ID_PENGAJUAN', 'ID_PENGAJUAN');
    }

    public function approvalLog()
    {
        return $this->hasMany(ApprovalLog::class, 'ID_PENGAJUAN', 'ID_PENGAJUAN');
    }

    public function detailKgb()
    {
        return $this->hasOne(DetailKgb::class, 'ID_PENGAJUAN', 'ID_PENGAJUAN');
    }
}
