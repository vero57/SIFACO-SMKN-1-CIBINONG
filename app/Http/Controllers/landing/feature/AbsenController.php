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
use App\Events\AttendanceCreated;
use Illuminate\Http\JsonResponse;

class AbsenController extends Controller
{
    private const TIMEZONE = 'Asia/Jakarta';
    private const STATUS_PRESENT = 'Hadir';
    private const STATUS_LATE = 'Telat';
    private const STATUS_SICK = 'Sakit';
    private const STATUS_PERMISSION = 'Izin';
    private const LATE_RULE_NAME = 'Terlambat';

    public function index()
    {
        return view('landing.feature.absen.index');
    }

    /**
     * Proses check-in absen siswa.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('Unauthorized', 401);
        }

        $classId = $this->getUserClassId($user);
        if (!$classId) {
            return $this->errorResponse('Kelas tidak ditemukan. Hubungi administrator.', 422);
        }

        $schedule = $this->getSchedule($classId);
        if (!$schedule) {
            return $this->errorResponse('Jadwal absen kelas belum diatur. Hubungi administrator.', 422);
        }

        $nowWIB = now(self::TIMEZONE);
        $today  = $nowWIB->toDateString();

        if ($this->hasCheckedInToday($user->id, $today)) {
            return $this->errorResponse('Anda sudah melakukan absen hari ini.');
        }

        if (!$this->isFaceMatch($request, $user->name)) {
            return $this->errorResponse('Wajah tidak dikenali. Absen ditolak.', 403);
        }

        if (!$request->filled('photo')) {
            return $this->errorResponse('Foto wajah tidak terkirim. Silakan ulangi absen.', 422);
        }

        $distance = $this->getDistanceFromSchool($request);
        if ($distance !== null && $distance > (float) config('school.radius')) {
            return $this->errorResponse(
                'Lokasi Anda berada di luar radius sekolah (' . round($distance, 1) . ' meter). Absen tidak dapat dilakukan.',
                422
            );
        }

        $statusName = $this->resolveAttendanceStatus($user->id, $today, $nowWIB->format('H:i:s'), $schedule);
        $status = AttendanceStatus::where('name', $statusName)->first();
        if (!$status) {
            return $this->errorResponse("Status '{$statusName}' tidak ditemukan di database.", 422);
        }

        $photoPath = $this->saveBase64Photo($request->photo, 'attendance_photos', 'attendance_' . $user->id);
        if (!$photoPath) {
            return $this->errorResponse('Format foto wajah tidak valid. Silakan ulangi absen.', 422);
        }

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

        event(new AttendanceCreated($absen));
        $this->recordLateViolationIfNeeded($absen, $statusName);

        return $this->successResponse([
            'status'  => $statusName,
            'message' => "Absen berhasil. Status: {$statusName}.",
        ]);
    }

    /**
     * Proses check-out absen siswa.
     */
    public function checkout(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('Unauthorized', 401);
        }

        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
        ]);

        $attendance = Attendance::findOrFail($request->attendance_id);

        if ((int) $attendance->student_id !== (int) $user->id) {
            return $this->errorResponse('Akses ditolak.', 403);
        }

        if ($attendance->time_out) {
            return $this->errorResponse('Anda sudah melakukan check-out hari ini.');
        }

        if (!$attendance->time_in) {
            return $this->errorResponse('Belum melakukan check-in.');
        }

        $classId = $attendance->class_id ?? $this->getUserClassId($user);
        if (!$classId) {
            return $this->errorResponse('Kelas tidak ditemukan.', 422);
        }

        $schedule = $this->getSchedule($classId);
        if (!$schedule) {
            return $this->errorResponse('Jadwal kelas tidak ditemukan.', 422);
        }

        $nowWIB  = now(self::TIMEZONE);
        $nowTime = $nowWIB->format('H:i:s');
        $today   = $nowWIB->toDateString();

        if ($attendance->date !== $today) {
            return $this->errorResponse('Tidak dapat check-out untuk tanggal yang sudah lewat.');
        }

        if ($nowTime < $schedule->start_time_out) {
            return $this->errorResponse(
                'Belum waktunya pulang. Check-out dapat dilakukan mulai pukul ' . substr($schedule->start_time_out, 0, 5) . ' WIB.'
            );
        }

        $attendance->update(['time_out' => $nowTime]);

        Log::info('[Absen] Check-out berhasil', [
            'user_id'       => $user->id,
            'attendance_id' => $attendance->id,
            'time_out'      => $nowTime,
            'date'          => $today,
        ]);

        return $this->successResponse([
            'time_out'      => $nowTime,
            'attendance_id' => $attendance->id,
            'message'       => 'Check-out berhasil pada jam ' . substr($nowTime, 0, 5) . ' WIB.',
        ]);
    }

    private function getUserClassId($user): ?int
    {
        return $user->classes()->value('classes.id');
    }

    private function getSchedule(int $classId): ?AttendanceSchedule
    {
        return AttendanceSchedule::where('class_id', $classId)->first();
    }

    private function hasCheckedInToday(int $studentId, string $date): bool
    {
        return Attendance::where('student_id', $studentId)
            ->where('date', $date)
            ->exists();
    }

    private function isFaceMatch(Request $request, string $studentName): bool
    {
        return !$request->has('face_label')
            || strtolower($request->face_label) === strtolower($studentName);
    }

    private function getDistanceFromSchool(Request $request): ?float
    {
        if (!$request->filled('lat') || !$request->filled('lng')) {
            return null;
        }

        return $this->calculateDistanceInMeters(
            (float) $request->lat,
            (float) $request->lng,
            (float) config('school.latitude'),
            (float) config('school.longitude')
        );
    }

    private function resolveAttendanceStatus(
        int $studentId,
        string $date,
        string $currentTime,
        AttendanceSchedule $schedule
    ): string {
        $approvedPermission = Permission::where('student_id', $studentId)
            ->whereIn('type', ['sakit', 'izin'])
            ->where('status', 'approved')
            ->whereDate('created_at', $date)
            ->first();

        if ($approvedPermission) {
            return $approvedPermission->type === 'sakit'
                ? self::STATUS_SICK
                : self::STATUS_PERMISSION;
        }

        return $currentTime > $schedule->end_time_in
            ? self::STATUS_LATE
            : self::STATUS_PRESENT;
    }

    private function recordLateViolationIfNeeded(Attendance $attendance, string $statusName): void
    {
        if ($statusName !== self::STATUS_LATE) {
            return;
        }

        $rule = ViolationRule::where('name', self::LATE_RULE_NAME)->first();
        if (!$rule) {
            return;
        }

        ViolationPoint::create([
            'student_id' => $attendance->student_id,
            'attendance_id' => $attendance->id,
            'rule_id' => $rule->id,
        ]);
    }

    private function calculateDistanceInMeters(
        float $lat1, float $lng1,
        float $lat2, float $lng2
    ): float {
        $earthRadius = 6371000;
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
        $imageData = base64_decode(substr($base64Data, strpos($base64Data, ',') + 1), true);
        if ($imageData === false) {
            return null;
        }

        $filename = $prefix . '_' . time() . '.' . $extension;
        $path     = $directory . '/' . $filename;

        Storage::disk('public')->put($path, $imageData);

        return 'storage/' . $path;
    }

    private function errorResponse(string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    private function successResponse(array $payload = [], int $status = 200): JsonResponse
    {
        return response()->json(['success' => true] + $payload, $status);
    }
}
