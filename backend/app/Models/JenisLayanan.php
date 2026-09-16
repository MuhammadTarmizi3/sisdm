<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisLayanan extends Model
{
    protected $table = 'jenis_layanan';
    protected $primaryKey = 'ID_LAYANAN';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'STATUS_LAYANAN' => 'boolean',
    ];

    public function persyaratanMaster()
    {
        return $this->hasMany(PersyaratanMaster::class, 'ID_LAYANAN', 'ID_LAYANAN');
    }

    public function tahapanApproval()
    {
        return $this->hasMany(TahapanApproval::class, 'ID_LAYANAN', 'ID_LAYANAN');
    }

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'ID_LAYANAN', 'ID_LAYANAN');
    }
}
