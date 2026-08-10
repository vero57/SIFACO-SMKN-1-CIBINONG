<?php

namespace App\Http\Controllers\dashboard\dash_feature;

use App\Exports\ViolationExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ViolationPoint;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PelanggaranController extends Controller
{
    public function index(Request $request)
    {
        // Check if it's an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return $this->searchAjax($request);
        }

        $user = Auth::user();
        $search = $request->input('search');
        $query = \App\Models\ViolationPoint::with(['student.classes', 'rule'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('created_at');

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

        $violations = $query->paginate(10)->appends(['search' => $search]);
        return view('dashboard.page.pelanggaran_page.index', compact('violations', 'search'));
    }

    /**
     * Search pelanggaran via AJAX
     */
    public function searchAjax(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $query = \App\Models\ViolationPoint::with(['student.classes', 'rule'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('created_at');

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

        $violations = $query->get();
        return response()->json([
            'data' => $violations,
            'info' => "Ditemukan {$violations->count()} pelanggaran"
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $violation = \App\Models\ViolationPoint::with(['student.classes', 'rule', 'attendance','student.studentDetail'])->findOrFail($id);

        // Jika guru, pastikan hanya bisa lihat murid di kelasnya
        if ($user->role && strtolower($user->role->name) === 'guru') {
            $kelasWali = \App\Models\ClassModel::where('walas_id', $user->id)->first();
            if (!$kelasWali || !$kelasWali->students->contains('id', $violation->student_id)) {
                abort(403, 'Anda tidak berhak melihat pelanggaran ini.');
            }
        }

        return view('dashboard.page.pelanggaran_page.show', compact('violation'));
    }

    public function exportExcel()
    {
        $violations = ViolationPoint::with(['rule', 'student'])->get();

        return Excel::download(new ViolationExport($violations), 'daftar_pelanggaran_siswa.xlsx');
    }

    public function destroy($id)
    {
        $violation = \App\Models\ViolationPoint::findOrFail($id);
        $violation->delete();
        return response()->json(['success' => true]);
    }
}
