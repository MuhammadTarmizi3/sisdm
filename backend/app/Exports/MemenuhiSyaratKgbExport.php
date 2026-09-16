<?php

namespace App\Exports;

use App\Models\Pengajuan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MemenuhiSyaratKgbExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Pengajuan::query()
            ->with(['pegawai.unitKerja', 'pegawai.pangkatGolongan'])
            ->where('STATUS_PENGAJUAN', 'Disetujui')
            ->whereHas('jenisLayanan', fn ($q) => $q->where('KODE_LAYANAN', 'KGB'));

        if (!empty($this->filters['id_unit'])) {
            $query->whereHas('pegawai', fn ($q) => $q->where('ID_UNIT', $this->filters['id_unit']));
        }

        if (!empty($this->filters['id_pangkat'])) {
            $query->whereHas('pegawai', fn ($q) => $q->where('ID_PANGKAT', $this->filters['id_pangkat']));
        }

        if (!empty($this->filters['dari_tanggal'])) {
            $query->where('updated_at', '>=', $this->filters['dari_tanggal']);
        }

        if (!empty($this->filters['sampai_tanggal'])) {
            $query->where('updated_at', '<=', $this->filters['sampai_tanggal']);
        }

        return $query->orderBy('updated_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No. Pengajuan',
            'NIP/NRP',
            'Nama Pegawai',
            'Unit Kerja',
            'Pangkat/Golongan',
            'Tanggal Pengajuan',
            'Tanggal Disetujui',
            'Nomor SK',
        ];
    }

    public function map($pengajuan): array
    {
        return [
            $pengajuan->ID_PENGAJUAN,
            $pengajuan->pegawai->NIP_NRP ?? '-',
            $pengajuan->pegawai->NAMA_PEGAWAI ?? '-',
            $pengajuan->pegawai->unitKerja?->NAMA_UNIT ?? '-',
            $pengajuan->pegawai->pangkatGolongan?->NAMA_PANGKAT ?? '-',
            $pengajuan->TANGGAL_PENGAJUAN?->format('d-m-Y') ?? '-',
            $pengajuan->updated_at?->format('d-m-Y') ?? '-',
            $pengajuan->NOMER_SK ?? '-',
        ];
    }
}
