<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JournalExport implements FromCollection, WithHeadings
{
    protected Collection $journals;

    public function __construct(Collection $journals)
    {
        $this->journals = $journals;
    }

    public function collection(): Collection
    {
        return $this->journals->map(function ($journal) {
            return [
                'Nama Siswa' => $journal->student?->name ?? '-',
                'Pelajaran' => $journal->subject?->name ?? '-',
                'Tanggal' => optional($journal->created_at)->format('Y-m-d H:i') ?? '-',
                'Deskripsi' => $journal->description ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama Siswa', 'Pelajaran', 'Tanggal', 'Deskripsi'];
    }
}
