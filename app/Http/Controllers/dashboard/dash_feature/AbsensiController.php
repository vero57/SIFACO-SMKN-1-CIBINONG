<?php

namespace App\Http\Controllers\dashboard\dash_feature;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\AttendanceStatus;
use Illuminate\Http\Request;
use Dompdf\Dompdf;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Check if it's an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return $this->searchAjax($request);
        }

        $search = $request->input('search');
        $kelas_id = $request->input('kelas');
        $status_id = $request->input('status');
        $tanggal = $request->input('tanggal');

        $attendances = Attendance::with(['student', 'status', 'student.studentDetail', 'student.classes'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhereHas('studentDetail', function ($q2) use ($search) {
                            $q2->where('nis', 'like', '%' . $search . '%')
                                ->orWhere('nisn', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('classes', function ($q3) use ($search) {
                            $q3->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($kelas_id, function ($query) use ($kelas_id) {
                $query->whereHas('student.classes', function ($q) use ($kelas_id) {
                    $q->where('classes.id', $kelas_id);
                });
            })
            ->when($status_id, function ($query) use ($status_id) {
                $query->where('status_id', $status_id);
            })
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->where('date', $tanggal);
            })
            ->orderByDesc('date')
            ->orderByDesc('time_in')
            ->paginate(10)
            ->appends([
                'search' => $search,
                'kelas' => $kelas_id,
                'status' => $status_id,
                'tanggal' => $tanggal,
            ]);

        // Ambil user login
        $user = auth()->user();

        // Filter kelas berdasarkan role
        if ($user && $user->role && $user->role->name == 'Guru') {
            $kelasList = ClassModel::where('walas_id', $user->id)->orderBy('name')->get();
        } else {
            $kelasList = ClassModel::orderBy('name')->get();
        }
        $statusList = AttendanceStatus::orderBy('name')->get();

        return view('dashboard.page.absensi_page.index', compact('attendances', 'search', 'kelas_id', 'status_id', 'tanggal', 'kelasList', 'statusList'));
    }

    /**
     * Search presensi via AJAX
     */
    public function searchAjax(Request $request)
    {
        $search = $request->input('search');
        $kelas_id = $request->input('kelas');
        $status_id = $request->input('status');
        $tanggal = $request->input('tanggal');

        $attendances = Attendance::with(['student', 'status', 'student.studentDetail', 'student.classes'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhereHas('studentDetail', function ($q2) use ($search) {
                            $q2->where('nis', 'like', '%' . $search . '%')
                                ->orWhere('nisn', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('classes', function ($q3) use ($search) {
                            $q3->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($kelas_id, function ($query) use ($kelas_id) {
                $query->whereHas('student.classes', function ($q) use ($kelas_id) {
                    $q->where('classes.id', $kelas_id);
                });
            })
            ->when($status_id, function ($query) use ($status_id) {
                $query->where('status_id', $status_id);
            })
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->where('date', $tanggal);
            })
            ->orderByDesc('date')
            ->orderByDesc('time_in')
            ->get();

        return response()->json([
            'data' => $attendances,
            'info' => "Ditemukan {$attendances->count()} presensi"
        ]);
    }

    public function show($attendance_id)
    {
        $attendance = Attendance::with(['student', 'status'])->findOrFail($attendance_id);
        return view('dashboard.page.absensi_page.show', compact('attendance'));
    }

    public function exportPdf()
    {
        $attendances = Attendance::with(['student', 'status'])
            ->orderByDesc('date')
            ->orderByDesc('time_in')
            ->get();

        $dompdf = new Dompdf();
        $html = view('dashboard.page.absensi_page.pdf', compact('attendances'))->render();
        $dompdf->loadHtml($html);
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="daftar_absensi_siswa.pdf"'
        ]);
    }
}
