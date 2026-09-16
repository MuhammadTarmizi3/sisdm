<?php

namespace App\Services;

use App\Models\LogAktivitas;
use Carbon\Carbon;

class AuditService
{
    public function log(int $userId, string $aktivitas, ?string $entityType = null, ?int $entityId = null): void
    {
        LogAktivitas::create([
            'ID_USER' => $userId,
            'AKTIVITAS' => $aktivitas,
            'ENTITY_TYPE' => $entityType,
            'ENTITY_ID' => $entityId,
            'WAKTU' => Carbon::now(),
        ]);
    }
}
