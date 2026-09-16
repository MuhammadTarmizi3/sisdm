<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NotifikasiService
{
    public function kirim(int $userId, string $judul, string $pesan, string $jenis): Notifikasi
    {
        return Notifikasi::create([
            'USER_ID' => $userId,
            'JUDUL' => $judul,
            'PESAN' => $pesan,
            'JENIS' => $jenis,
            'IS_READ' => false,
            'CREATED_AT' => Carbon::now(),
        ]);
    }

    public function kirimKeRole(string $kodeRole, string $judul, string $pesan, string $jenis): void
    {
        // Uses spatie/laravel-permission scope `role`
        $users = User::role($kodeRole)->get();

        foreach ($users as $user) {
            $this->kirim($user->USER_ID, $judul, $pesan, $jenis);
        }
    }

    public function tandaiDibaca(int $notifikasiId): void
    {
        Notifikasi::where('ID_NOTIFIKASI', $notifikasiId)->update(['IS_READ' => true]);
    }

    public function getUnread(int $userId): Collection
    {
        return Notifikasi::where('USER_ID', $userId)
            ->where('IS_READ', false)
            ->get();
    }
}
