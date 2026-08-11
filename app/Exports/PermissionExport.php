<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PermissionExport implements FromCollection, WithHeadings
{
    protected Collection $permissions;

    public function __construct(Collection $permissions)
    {
        $this->permissions = $permissions;
    }

    public function collection(): Collection
    {
        return $this->permissions->map(function ($permission) {
            return [
                'Nama Siswa' => $permission->student?->name ?? '-',
                'Hari, Tanggal' => optional($permission->created_at)->format('Y-m-d') ?? '-',
                'Nama Orang Tua' => $permission->parent_name ?? '-',
                'Jenis Izin' => $permission->type ?? '-',
                'Status' => $permission->status ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama Siswa', 'Hari, Tanggal', 'Nama Orang Tua', 'Jenis Izin', 'Status'];
    }
}
