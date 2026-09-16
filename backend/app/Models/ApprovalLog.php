<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    protected $table = 'approval_log';
    protected $primaryKey = 'ID_APPROVAL_LOG';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'WAKTU' => 'datetime',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'ID_PENGAJUAN', 'ID_PENGAJUAN');
    }

    public function tahapanApproval()
    {
        return $this->belongsTo(TahapanApproval::class, 'ID_TAHAPAN', 'ID_TAHAPAN');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'ID_USER', 'USER_ID');
    }
}
