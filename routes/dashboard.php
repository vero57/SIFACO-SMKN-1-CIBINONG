<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\DashboardController;
use App\Http\Controllers\dashboard\dash_feature\AbsensiController;
use App\Http\Controllers\dashboard\dash_feature\JurnalController;
use App\Http\Controllers\dashboard\dash_feature\UsersController;
use App\Http\Controllers\dashboard\dash_feature\PelanggaranController;
use App\Http\Controllers\dashboard\dash_feature\IzinController;
use App\Http\Controllers\dashboard\dash_feature\SiswaController;
use App\Http\Controllers\dashboard\dash_feature\SubjectController;
use App\Http\Controllers\dashboard\dash_feature\KelasController;

// Route untuk Guru & Admin (fitur yang boleh diakses Guru)
Route::middleware(['userakses:Admin,Guru'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.dash');

    // Kelas
    Route::resource('/dashboard/kelas', KelasController::class, [
        'as' => 'dashboard'
    ]);
    Route::post('/dashboard/kelas/{kelas}/update-walas', [KelasController::class, 'updateWalas'])->name('dashboard.kelas.updateWalas');
    Route::post('/dashboard/kelas/{kelas}/add-students', [KelasController::class, 'addStudents'])->name('dashboard.kelas.addStudents');
    Route::post('/dashboard/kelas/{kelas}/remove-students', [KelasController::class, 'removeStudents'])->name('dashboard.kelas.removeStudents');
    Route::post('/dashboard/kelas/{kelas}/update-schedule', [KelasController::class, 'updateSchedule'])->name('dashboard.kelas.updateSchedule');
    Route::post('/dashboard/kelas/{kelas}/update-name', [KelasController::class, 'updateName'])->name('dashboard.kelas.updateName');

    // Mapel (Subjects)
    Route::resource('/dashboard/subjects', SubjectController::class, [
        'as' => 'dashboard'
    ]);

    // Presensi (Absensi)
    Route::get('/dashboard/absensi', [AbsensiController::class, 'index'])->name('dashboard.absensi');
    Route::get('/dashboard/absensi/{user_id}/show', [AbsensiController::class, 'show'])->name('dashboard.absensi.show');
    Route::get('/dashboard/absensi/export/pdf', [AbsensiController::class, 'exportPdf'])->name('dashboard.absensi.exportPdf');

    // Jurnal
    Route::get('/dashboard/jurnal', [JurnalController::class, 'index'])->name('dashboard.jurnal');
    Route::get('/dashboard/jurnal/{id}/show', [JurnalController::class, 'show'])->name('dashboard.jurnal.show');
    Route::get('/dashboard/jurnal/export/pdf', [JurnalController::class, 'exportPdf'])->name('dashboard.jurnal.exportPdf');

    // Pelanggaran
    Route::get('/dashboard/pelanggaran', [PelanggaranController::class, 'index'])->name('dashboard.pelanggaran');
    Route::get('/dashboard/pelanggaran/{id}/show', [PelanggaranController::class, 'show'])->name('dashboard.pelanggaran.show');
    Route::get('/dashboard/pelanggaran/export/pdf', [PelanggaranController::class, 'exportPdf'])->name('dashboard.pelanggaran.exportPdf');
    Route::delete('/dashboard/pelanggaran/{id}/delete', [PelanggaranController::class, 'destroy'])->name('dashboard.pelanggaran.delete');

    // Izin
    Route::get('/dashboard/izin', [IzinController::class, 'index'])->name('dashboard.izin');
    Route::get('dashboard/izin/{id}', [IzinController::class, 'show'])->name('dashboard.izin.show');
    Route::post('/dashboard/izin/{id}/update-status', [IzinController::class, 'updateStatus'])->name('dashboard.izin.updateStatus');
    Route::get('/dashboard/izin/export/pdf', [IzinController::class, 'exportPdf'])->name('dashboard.izin.exportPdf');
});

// Route khusus Admin (akses penuh, termasuk users & siswa)
Route::middleware(['userakses:Admin'])->group(
    function () {
        // Users
        Route::resource('/dashboard/users', UsersController::class, [
            'as' => 'dashboard'
        ]);

        // Siswa (semua fitur siswa hanya admin)
        Route::get('/dashboard/siswa', [SiswaController::class, 'index'])->name('dashboard.siswa');
        Route::get('/dashboard/siswa/{user}/detail', [SiswaController::class, 'createDetail'])->name('dashboard.siswa.detail.create');
        Route::post('/dashboard/siswa/{user}/detail', [SiswaController::class, 'storeDetail'])->name('dashboard.siswa.detail.store');
        Route::get('/dashboard/siswa/{user}/detail/edit', [SiswaController::class, 'editDetail'])->name('dashboard.siswa.detail.edit');
        Route::post('/dashboard/siswa/{user}/detail/update', [SiswaController::class, 'updateDetail'])->name('dashboard.siswa.detail.update');
        Route::get('/dashboard/siswa/{user}/show', [SiswaController::class, 'show'])->name('dashboard.siswa.detail.show');

        // Import Data Siswa
        Route::post('/dashboard/siswa/import', [SiswaController::class, 'import'])->name('dashboard.siswa.import');
    }
);
