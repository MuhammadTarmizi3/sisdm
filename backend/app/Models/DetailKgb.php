<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKgb extends Model
{
    protected $table = 'detail_kgb';
    protected $primaryKey = 'ID_PENGAJUAN';
    protected $keyType = 'int';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'TMT_KGB_BERIKUTNYA' => 'date',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'ID_PENGAJUAN', 'ID_PENGAJUAN');
    }
}
