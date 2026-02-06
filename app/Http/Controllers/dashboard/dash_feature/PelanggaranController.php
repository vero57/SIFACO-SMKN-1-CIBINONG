<?php

namespace App\Http\Controllers\dashboard\dash_feature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ViolationPoint;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;

class PelanggaranController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = \App\Models\ViolationPoint::with(['student.classes', 'rule'])->orderByDesc('created_at');

        // Jika user adalah guru (bukan admin), filter hanya murid di kelas yang diwalikan
        if ($user->role && strtolower($user->role->name) === 'guru') {
            // Ambil kelas yang diwalikan oleh guru ini
            $kelasWali = \App\Models\ClassModel::where('walas_id', $user->id)->first();
            if ($kelasWali) {
                $studentIds = $kelasWali->students()->pluck('users.id')->toArray();
                $query->whereIn('student_id', $studentIds);
            } else {
                // Jika tidak wali kelas, tampilkan kosong
                $query->whereRaw('0=1');
            }
        }
        // Admin bisa melihat semua

        $violations = $query->get();
        return view('dashboard.page.pelanggaran_page.index', compact('violations'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $violation = \App\Models\ViolationPoint::with(['student.classes', 'rule', 'attendance'])->findOrFail($id);

        // Jika guru, pastikan hanya bisa lihat murid di kelasnya
        if ($user->role && strtolower($user->role->name) === 'guru') {
            $kelasWali = \App\Models\ClassModel::where('walas_id', $user->id)->first();
            if (!$kelasWali || !$kelasWali->students->contains('id', $violation->student_id)) {
                abort(403, 'Anda tidak berhak melihat pelanggaran ini.');
            }
        }

        return view('dashboard.page.pelanggaran_page.show', compact('violation'));
    }

    public function exportPdf()
    {
        $violations = ViolationPoint::with(['rule', 'student'])->get();
        return response((new \Dompdf\Dompdf)->loadHtml(
            view('dashboard.page.pelanggaran_page.pdf', compact('violations'))
        )->setPaper('A4', 'landscape')->render()->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="pelanggaran.pdf"');
    }

    public function destroy($id)
    {
        $violation = \App\Models\ViolationPoint::findOrFail($id);
        $violation->delete();
        return response()->json(['success' => true]);
    }
}
