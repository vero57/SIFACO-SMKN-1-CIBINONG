<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->role && auth()->user()->role->name === 'Siswa') {
            return redirect()->route('landing.home');
        }

        // Ambil tanggal hari ini
        $today = Carbon::today();
        $todayString = $today->toDateString();

        // Ambil data siswa dengan izin approved hari ini
        $approvedPermissionsToday = Permission::whereIn('type', ['sakit', 'izin', 'dispen'])
            ->where('status', 'approved')
            ->whereDate('created_at', $todayString)
            ->with('student')
            ->get();

        // Hitung jumlah siswa berdasarkan type
        $totalSiswa = User::whereHas('role', function($q) {
            $q->where('name', 'Siswa');
        })->count();

        $siswaSakit = $approvedPermissionsToday->where('type', 'sakit')->count();
        $siswaIzin = $approvedPermissionsToday->where('type', 'izin')->count();
        $siswaDispen = $approvedPermissionsToday->where('type', 'dispen')->count();

        // Siswa masuk: hanya siswa yang sudah melakukan presensi hari ini
        $siswaMasuk = Attendance::where('date', $todayString)->count();

        // Siswa tanpa keterangan/alfa: siswa yang tidak absen dan tidak punya izin/sakit/dispen approved hari ini
        // Ambil ID siswa yang sudah absen hari ini
        $siswaAbsenIds = Attendance::where('date', $todayString)->pluck('student_id')->toArray();

        // Ambil ID siswa yang punya izin/sakit/dispen approved hari ini
        $siswaIzinIds = Permission::whereIn('type', ['sakit', 'izin', 'dispen'])
            ->where('status', 'approved')
            ->whereDate('created_at', $todayString)
            ->pluck('student_id')
            ->toArray();

        // Gabungkan ID siswa yang sudah absen atau punya izin
        $siswaYangTerakuniIds = array_unique(array_merge($siswaAbsenIds, $siswaIzinIds));

        // Hitung siswa yang belum absen dan tidak punya izin (Alfa)
        $siswaAlfa = User::whereHas('role', function($q) {
            $q->where('name', 'Siswa');
        })->whereNotIn('id', $siswaYangTerakuniIds)->count();

        return view('dashboard.dash.index', compact(
            'approvedPermissionsToday',
            'totalSiswa',
            'siswaMasuk',
            'siswaSakit',
            'siswaIzin',
            'siswaDispen',
            'siswaAlfa'
        ));
    }
}
