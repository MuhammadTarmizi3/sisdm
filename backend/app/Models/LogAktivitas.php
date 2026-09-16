<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';
    protected $primaryKey = 'ID_LOG';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'WAKTU' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'ID_USER', 'USER_ID');
    }
}
