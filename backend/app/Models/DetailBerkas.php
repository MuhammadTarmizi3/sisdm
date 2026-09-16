<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailBerkas extends Model
{
    protected $table = 'detail_berkas';
    protected $primaryKey = 'ID_DETAIL_BERKAS';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = [];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'ID_PENGAJUAN', 'ID_PENGAJUAN');
    }

    public function persyaratanMaster()
    {
        return $this->belongsTo(PersyaratanMaster::class, 'ID_PERSYARATAN', 'ID_PERSYARATAN');
    }
}
