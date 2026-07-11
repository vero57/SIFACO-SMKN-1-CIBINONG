<?php

namespace Tests\Feature;

use App\Exports\AttendanceExport;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\User;
use Tests\TestCase;

class AttendanceExportTest extends TestCase
{
    public function test_it_builds_excel_rows_with_expected_headers(): void
    {
        $attendance = new Attendance([
            'date' => '2026-07-11',
            'time_in' => '07:00:00',
            'time_out' => '15:00:00',
        ]);

        $attendance->setRelation('student', new User(['name' => 'Budi']));
        $attendance->setRelation('status', new AttendanceStatus(['name' => 'Hadir']));

        $export = new AttendanceExport(collect([$attendance]));
        $rows = $export->collection();

        $this->assertSame('Budi', $rows->first()['Nama Siswa']);
        $this->assertSame('Hadir', $rows->first()['Status Kehadiran']);
        $this->assertSame([
            'Nama Siswa',
            'Kelas Siswa',
            'Tanggal',
            'Waktu Masuk',
            'Waktu Keluar',
            'Status Kehadiran',
        ], $export->headings());
    }
}
