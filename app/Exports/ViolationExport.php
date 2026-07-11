<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ViolationExport implements FromCollection, WithHeadings
{
    protected Collection $violations;

    public function __construct(Collection $violations)
    {
        $this->violations = $violations;
    }

    public function collection(): Collection
    {
        return $this->violations->map(function ($violation) {
            return [
                'Nama Siswa' => $violation->student?->name ?? '-',
                'Pelanggaran' => $violation->rule?->name ?? '-',
                'Poin' => $violation->rule?->points ?? '-',
                'Tanggal' => optional($violation->created_at)->format('Y-m-d') ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama Siswa', 'Pelanggaran', 'Poin', 'Tanggal'];
    }
}
