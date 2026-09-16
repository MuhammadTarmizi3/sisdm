<?php

namespace App\Exports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RekapKepegawaianExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Pegawai::query()
            ->with(['unitKerja', 'jabatan', 'pangkatGolongan'])
            ->whereNull('deleted_at');

        if (!empty($this->filters['id_unit'])) {
            $query->where('ID_UNIT', $this->filters['id_unit']);
        }

        if (!empty($this->filters['id_pangkat'])) {
            $query->where('ID_PANGKAT', $this->filters['id_pangkat']);
        }

        return $query->orderBy('NAMA_PEGAWAI');
    }

    public function headings(): array
    {
        return [
            'NIP/NRP',
            'Nama Pegawai',
            'Jenis Kelamin',
            'Unit Kerja',
            'Jabatan',
            'Pangkat/Golongan',
            'Status Kepegawaian',
            'TMT CPNS',
            'TMT PNS',
            'Pendidikan Terakhir',
        ];
    }

    public function map($pegawai): array
    {
        return [
            $pegawai->NIP_NRP,
            $pegawai->NAMA_PEGAWAI,
            $pegawai->JENIS_KELAMIN,
            $pegawai->unitKerja?->NAMA_UNIT ?? '-',
            $pegawai->jabatan?->NAMA_JABATAN ?? '-',
            $pegawai->pangkatGolongan?->NAMA_PANGKAT ?? '-',
            $pegawai->STATUS_KEPEGAWAIAN ?? '-',
            $pegawai->TMT_CPNS?->format('d-m-Y') ?? '-',
            $pegawai->TMT_PNS?->format('d-m-Y') ?? '-',
            $pegawai->PENDIDIKAN_TERAKHIR ?? '-',
        ];
    }
}
