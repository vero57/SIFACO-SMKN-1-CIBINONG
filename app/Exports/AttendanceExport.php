<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected Collection $attendances;

    public function __construct(Collection $attendances)
    {
        $this->attendances = $attendances;
    }

    public function collection(): Collection
    {
        return $this->attendances->map(function ($attendance) {
            $student = $attendance->student;
            $classes = $student && $student->relationLoaded('classes') || $student && $student->classes ? $student->classes : collect();
            $classNames = $classes->count() ? $classes->pluck('name')->join(', ') : '-';

            return [
                'Nama Siswa' => $student?->name ?? '-',
                'Kelas Siswa' => $classNames,
                'Tanggal' => $attendance->date ?? '-',
                'Waktu Masuk' => $attendance->time_in ?? '-',
                'Waktu Keluar' => $attendance->time_out ?? '-',
                'Status Kehadiran' => $attendance->status?->name ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Kelas Siswa',
            'Tanggal',
            'Waktu Masuk',
            'Waktu Keluar',
            'Status Kehadiran',
        ];
    }
}
