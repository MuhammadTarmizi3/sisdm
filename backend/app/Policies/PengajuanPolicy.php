<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pengajuan;

class PengajuanPolicy
{
    public function view(User $user, Pengajuan $pengajuan): bool
    {
        if ($user->hasRole(['admin_kepegawaian', 'verifikator'])) {
            return true;
        }

        return $pengajuan->ID_PEGAWAI === $user->pegawai?->ID_PEGAWAI;
    }

    public function update(User $user, Pengajuan $pengajuan): bool
    {
        return $this->view($user, $pengajuan);
    }
}
