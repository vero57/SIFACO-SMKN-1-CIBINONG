<?php

namespace App\Http\Controllers\landing\feature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\AttendanceSchedule;
use App\Models\ViolationPoint;
use App\Models\ViolationRule;

class AbsenController extends Controller
{
    public function index()
    {
        return view('landing.feature.absen.index');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Ambil class_id user (menggunakan relasi belongsToMany 'classes')
        $classId = $user->classes()->first()->id ?? null;
        if (!$classId) {
            return response()->json(['success' => false, 'message' => 'Class ID tidak ditemukan'], 422);
        }

        // Ambil jadwal absen kelas
        $schedule = AttendanceSchedule::where('class_id', $classId)->first();
        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Jadwal absen kelas tidak ditemukan'], 422);
        }

        // Ambil waktu sekarang dalam zona Asia/Jakarta (WIB)
        $nowWIB = now('Asia/Jakarta');
        $today = $nowWIB->toDateString();

        // Cek apakah sudah absen hari ini
        $sudahAbsen = Attendance::where('student_id', $user->id)
            ->where('date', $today)
            ->first();
        if ($sudahAbsen) {
            return response()->json(['success' => false, 'message' => 'Sudah absen hari ini']);
        }

        // Validasi label wajah (jika dikirim dari frontend)
        if ($request->has('face_label') && strtolower($request->face_label) !== strtolower($user->name)) {
            return response()->json(['success' => false, 'message' => 'Wajah tidak dikenali sebagai user. Absen ditolak.'], 403);
        }

        // Cek waktu absen
        $startTimeIn = $schedule->start_time_in; // format: 'H:i:s'
        $endTimeIn = $schedule->end_time_in;     // format: 'H:i:s'
        $nowTime = $nowWIB->format('H:i:s');

        // if ($nowTime < $startTimeIn) {
        //     return response()->json(['success' => false, 'message' => 'Belum waktunya absen. Silakan absen mulai pukul ' . $startTimeIn . ' WIB.']);
        // }

        // Status default Hadir
        $statusName = 'Hadir';
        if ($nowTime > $endTimeIn) {
            $statusName = 'Telat';
        }
        $status = \App\Models\AttendanceStatus::where('name', $statusName)->first();
        if (!$status) {
            return response()->json(['success' => false, 'message' => 'Status ' . $statusName . ' tidak ditemukan'], 422);
        }

        $photoPath = null;
        if ($request->photo) {
            // Proses base64 ke file
            $base64 = $request->photo;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $base64 = substr($base64, strpos($base64, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, etc
                $base64 = base64_decode($base64);
                $filename = 'attendance_' . $user->id . '_' . time() . '.' . $type;
                $path = 'attendance_photos/' . $filename;
                \Storage::disk('public')->put($path, $base64);
                $photoPath = 'storage/' . $path;
            }
        }

        $absen = Attendance::create([
            'student_id' => $user->id,
            'class_id' => $classId,
            'date' => $today,
            'time_in' => $nowWIB->format('H:i:s'),
            'status_id' => $status->id,
            'location_lat' => $request->lat,
            'location_lng' => $request->lng,
            'photo' => $photoPath
        ]);

        // Tambahkan pelanggaran jika status Telat
        if ($statusName === 'Telat') {
            $rule = ViolationRule::where('name', 'Terlambat')->first();
            if ($rule) {
                ViolationPoint::create([
                    'student_id' => $user->id,
                    'attendance_id' => $absen->id,
                    'rule_id' => $rule->id,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'attendance_id' => 'required|exists:attendances,id'
        ]);

        // Ambil data absen
        $attendance = Attendance::findOrFail($request->attendance_id);

        // Validasi absen punya user yang login
        if ($attendance->student_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk absen ini'], 403);
        }

        // Validasiapakah sudah user udah keluar apa belum
        if ($attendance->time_out) {
            return response()->json(['success' => false, 'message' => 'Sudah check out hari ini']);
        }

        // Validasi sudah absen
        if (!$attendance->time_in) {
            return response()->json(['success' => false, 'message' => 'Belum melakukan check in']);
        }

        // Ambil class_id dari absen atau user
        $classId = $attendance->class_id ?? $user->classes()->first()->id ?? null;
        if (!$classId) {
            return response()->json(['success' => false, 'message' => 'Class ID tidak ditemukan'], 422);
        }

        // Ambil jadwal absen kelas
        $schedule = AttendanceSchedule::where('class_id', $classId)->first();
        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Jadwal absen kelas tidak ditemukan'], 422);
        }

        // Ambil waktu sekarangAsia/Jakarta (WIB)
        $nowWIB = now('Asia/Jakarta');
        $nowTime = $nowWIB->format('H:i:s');
        $today = $nowWIB->toDateString();

        // Validasi 4: Pastikan tanggal absen adalah hari ini
        if ($attendance->date != $today) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa check out untuk tanggal yang sudah lewat'
            ]);
        }

        // Cek apakah sudah waktunya check out
        $startTimeOut = $schedule->start_time_out;
        $endTimeOut = $schedule->end_time_out;

        // Validasi 5: Cek waktu check out
        if ($nowTime < $startTimeOut) {
            return response()->json([
                'success' => false,
                'message' => 'Belum waktunya pulang. Silakan check out mulai pukul ' . $startTimeOut . ' WIB.'
            ]);
        }

        // Optional: Validasi batas akhir check out
        // if ($nowTime > $endTimeOut) {
        //     return response()->json([
        //         'success' => false, 
        //         'message' => 'Waktu check out sudah lewat. Batas akhir check out pukul ' . $endTimeOut . ' WIB.'
        //     ]);
        // }

        // Update jam pulang
        $attendance->time_out = $nowTime;
        $attendance->save();

        // Log aktivitas (opsional)
        \Log::info('User check out', [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'time_out' => $nowTime,
            'date' => $today
        ]);

        return response()->json([
            'success' => true,
            'time_out' => $nowTime,
            'attendance_id' => $attendance->id,
            'message' => 'Check out berhasil pada jam ' . $nowTime
        ]);
    }

    // Fungsi untuk memberi poin Bolos jika siswa tidak absen (panggil via scheduler/command harian)
    public static function giveBolosForAbsentStudents($date = null)
    {
        $date = $date ?? now('Asia/Jakarta')->toDateString();
        $classSchedules = \App\Models\AttendanceSchedule::all();

        foreach ($classSchedules as $schedule) {
            $students = $schedule->class->students ?? [];
            foreach ($students as $student) {
                $sudahAbsen = \App\Models\Attendance::where('student_id', $student->id)
                    ->where('date', $date)
                    ->exists();
                if (!$sudahAbsen) {
                    $rule = ViolationRule::where('name', 'Bolos')->first();
                    if ($rule) {
                        ViolationPoint::firstOrCreate([
                            'student_id' => $student->id,
                            'rule_id' => $rule->id,
                            'attendance_id' => null,
                        ]);
                    }
                }
            }
        }
    }
}
