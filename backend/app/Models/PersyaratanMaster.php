<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersyaratanMaster extends Model
{
    protected $table = 'persyaratan_master';
    protected $primaryKey = 'ID_PERSYARATAN';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'WAJIB' => 'boolean',
        'IS_ACTIVE' => 'boolean',
    ];

    public function jenisLayanan()
    {
        return $this->belongsTo(JenisLayanan::class, 'ID_LAYANAN', 'ID_LAYANAN');
    }

    public function detailBerkas()
    {
        return $this->hasMany(DetailBerkas::class, 'ID_PERSYARATAN', 'ID_PERSYARATAN');
    }
}
