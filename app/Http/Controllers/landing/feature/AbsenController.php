<?php

namespace App\Http\Controllers\landing\feature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\AttendanceSchedule;
use App\Models\Permission;
use App\Models\ViolationPoint;
use App\Models\ViolationRule;

/**
 * AbsenController — menangani proses check-in dan check-out absen siswa.
 *
 * Alur absen check-in:
 * 1. Validasi user terautentikasi & memiliki kelas.
 * 2. Cek apakah sudah absen hari ini (tolak jika sudah).
 * 3. Cek apakah ada izin/sakit yang disetujui (status override).
 * 4. Validasi wajah (face_label dari frontend).
 * 5. Validasi radius lokasi (server-side double-check dari config).
 * 6. Tentukan status: Hadir / Telat / Sakit / Izin berdasarkan waktu & izin.
 * 7. Simpan data absen, foto, dan poin pelanggaran jika Telat.
 */
class AbsenController extends Controller
{
    public function index()
    {
        return view('landing.feature.absen.index');
    }

    /**
     * Proses check-in absen siswa.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Ambil class_id user dari relasi belongsToMany 'classes'
        $classId = $user->classes()->first()?->id ?? null;
        if (!$classId) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan. Hubungi administrator.'], 422);
        }

        // Ambil jadwal absen kelas
        $schedule = AttendanceSchedule::where('class_id', $classId)->first();
        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Jadwal absen kelas belum diatur. Hubungi administrator.'], 422);
        }

        // Waktu sekarang dalam zona WIB (Asia/Jakarta)
        $nowWIB = now('Asia/Jakarta');
        $today  = $nowWIB->toDateString();

        // Cek apakah sudah absen hari ini
        $sudahAbsen = Attendance::where('student_id', $user->id)
            ->where('date', $today)
            ->first();
        if ($sudahAbsen) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absen hari ini.']);
        }

        // Validasi face label dari frontend
        if ($request->has('face_label') && strtolower($request->face_label) !== strtolower($user->name)) {
            return response()->json(['success' => false, 'message' => 'Wajah tidak dikenali. Absen ditolak.'], 403);
        }

        // --- Validasi radius server-side ---
        // Frontend sudah validasi, tapi ini sebagai double-check keamanan.
        $schoolLat    = config('school.latitude');
        $schoolLng    = config('school.longitude');
        $radiusMeter  = config('school.radius');

        if ($request->filled('lat') && $request->filled('lng')) {
            $distance = $this->calculateDistanceInMeters(
                (float) $request->lat,
                (float) $request->lng,
                $schoolLat,
                $schoolLng
            );
            if ($distance > $radiusMeter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lokasi Anda berada di luar radius sekolah (' . round($distance, 1) . ' meter). Absen tidak dapat dilakukan.',
                ], 422);
            }
        }

        // --- Tentukan status absen ---
        $nowTime = $nowWIB->format('H:i:s');

        // Cek apakah ada Permission (izin/sakit) yang disetujui untuk hari ini
        $approvedPermission = Permission::where('student_id', $user->id)
            ->whereIn('type', ['sakit', 'izin'])
            ->where('status', 'approved')
            ->whereDate('created_at', $today)
            ->first();

        if ($approvedPermission) {
            // Jika ada izin yang disetujui, status absen mengikuti tipe izin
            $statusName = ($approvedPermission->type === 'sakit') ? 'Sakit' : 'Izin';
        } elseif ($nowTime > $schedule->end_time_in) {
            $statusName = 'Telat';
        } else {
            $statusName = 'Hadir';
        }

        $status = AttendanceStatus::where('name', $statusName)->first();
        if (!$status) {
            return response()->json(['success' => false, 'message' => "Status '{$statusName}' tidak ditemukan di database."], 422);
        }

        // --- Simpan foto dari base64 ---
        $photoPath = null;
        if ($request->photo) {
            $photoPath = $this->saveBase64Photo($request->photo, 'attendance_photos', 'attendance_' . $user->id);
        }

        // --- Simpan record absen ---
        $absen = Attendance::create([
            'student_id'   => $user->id,
            'class_id'     => $classId,
            'date'         => $today,
            'time_in'      => $nowWIB->format('H:i:s'),
            'status_id'    => $status->id,
            'location_lat' => $request->lat,
            'location_lng' => $request->lng,
            'photo'        => $photoPath,
        ]);

        event(new \App\Events\AttendanceCreated($absen));

        // Tambahkan poin pelanggaran jika Telat
        if ($statusName === 'Telat') {
            $rule = ViolationRule::where('name', 'Terlambat')->first();
            if ($rule) {
                ViolationPoint::create([
                    'student_id'    => $user->id,
                    'attendance_id' => $absen->id,
                    'rule_id'       => $rule->id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'status'  => $statusName,
            'message' => "Absen berhasil. Status: {$statusName}.",
        ]);
    }

    /**
     * Proses check-out absen siswa.
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
        ]);

        $attendance = Attendance::findOrFail($request->attendance_id);

        // Pastikan absen milik user yang sedang login
        if ($attendance->student_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if ($attendance->time_out) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan check-out hari ini.']);
        }

        if (!$attendance->time_in) {
            return response()->json(['success' => false, 'message' => 'Belum melakukan check-in.']);
        }

        $classId = $attendance->class_id ?? $user->classes()->first()?->id ?? null;
        if (!$classId) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan.'], 422);
        }

        $schedule = AttendanceSchedule::where('class_id', $classId)->first();
        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Jadwal kelas tidak ditemukan.'], 422);
        }

        $nowWIB  = now('Asia/Jakarta');
        $nowTime = $nowWIB->format('H:i:s');
        $today   = $nowWIB->toDateString();

        // Validasi: absen harus tanggal hari ini
        if ($attendance->date != $today) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat check-out untuk tanggal yang sudah lewat.']);
        }

        // Validasi: belum waktunya pulang
        if ($nowTime < $schedule->start_time_out) {
            return response()->json([
                'success' => false,
                'message' => 'Belum waktunya pulang. Check-out dapat dilakukan mulai pukul ' . substr($schedule->start_time_out, 0, 5) . ' WIB.',
            ]);
        }

        $attendance->time_out = $nowTime;
        $attendance->save();

        Log::info('[Absen] Check-out berhasil', [
            'user_id'       => $user->id,
            'attendance_id' => $attendance->id,
            'time_out'      => $nowTime,
            'date'          => $today,
        ]);

        return response()->json([
            'success'       => true,
            'time_out'      => $nowTime,
            'attendance_id' => $attendance->id,
            'message'       => 'Check-out berhasil pada jam ' . substr($nowTime, 0, 5) . ' WIB.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Hitung jarak dua titik koordinat dalam satuan meter menggunakan formula Haversine.
     */
    private function calculateDistanceInMeters(
        float $lat1, float $lng1,
        float $lat2, float $lng2
    ): float {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Simpan foto base64 ke storage dan kembalikan path relatifnya.
     * Mengembalikan null jika data tidak valid.
     */
    private function saveBase64Photo(string $base64Data, string $directory, string $prefix): ?string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
            return null;
        }

        $extension = strtolower($matches[1]);
        $imageData = base64_decode(substr($base64Data, strpos($base64Data, ',') + 1));

        $filename = $prefix . '_' . time() . '.' . $extension;
        $path     = $directory . '/' . $filename;

        Storage::disk('public')->put($path, $imageData);

        return 'storage/' . $path;
    }
}
