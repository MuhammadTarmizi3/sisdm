<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahapanApproval extends Model
{
    protected $table = 'tahapan_approval';
    protected $primaryKey = 'ID_APPROVAL';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    public function jenisLayanan()
    {
        return $this->belongsTo(JenisLayanan::class, 'ID_LAYANAN', 'ID_LAYANAN');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'ID_ROLE_BERWENANG', 'ID_ROLE');
    }
}
