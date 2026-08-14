<?php

namespace App\Http\Controllers\dashboard\dash_feature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\User;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array((int) $perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $user = auth()->user();
        if ($user && $user->role && $user->role->name === 'Guru') {
            $classes = ClassModel::with('walas')
                ->where('walas_id', $user->id)
                ->paginate($perPage)
                ->appends(['per_page' => $perPage]);
        } else {
            $classes = ClassModel::with('walas')
                ->paginate($perPage)
                ->appends(['per_page' => $perPage]);
        }

        return view('dashboard.page.kelas_page.index', compact('classes', 'perPage'));
    }

    public function create()
    {
        $teachers = User::whereHas('role', function ($q) {
            $q->where('name', 'Guru');
        })->get();
        return view('dashboard.page.kelas_page.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'walas_id' => 'required|exists:users,id'
        ]);
        ClassModel::create([
            'name' => $request->name,
            'walas_id' => $request->walas_id
        ]);
        return redirect()->route('dashboard.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $class = ClassModel::with(['walas', 'students', 'attendanceSchedule'])->findOrFail($id);
        $teachers = User::whereHas('role', function ($q) {
            $q->where('name', 'Guru');
        })->get();

        $availableStudents = User::whereHas('role', function ($q) {
            $q->where('name', 'Siswa');
        })
            ->whereDoesntHave('classes')
            ->get();

        return view('dashboard.page.kelas_page.show', compact('class', 'teachers', 'availableStudents'));
    }

    public function edit(string $id)
    {
    }

    public function update(Request $request, string $id)
    {
    }

    public function updateWalas(Request $request, $id)
    {
        $request->validate([
            'walas_id' => 'required|exists:users,id'
        ]);
        $class = ClassModel::findOrFail($id);
        $class->walas_id = $request->walas_id;
        $class->save();
        return redirect()->route('dashboard.kelas.show', $id)->with('success_walas', 'Wali Kelas berhasil diubah.');
    }

    public function addStudents(Request $request, $id)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id'
        ]);
        $class = ClassModel::findOrFail($id);
        $studentIds = User::whereIn('id', $request->student_ids)
            ->whereHas('role', function ($q) {
                $q->where('name', 'Siswa');
            })
            ->whereDoesntHave('classes')
            ->pluck('id')
            ->toArray();

        $class->students()->attach($studentIds);

        return redirect()->route('dashboard.kelas.show', $id)->with('success_siswa', 'Siswa berhasil ditambahkan ke kelas.');
    }

    public function removeStudents(Request $request, $id)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id'
        ]);
        $class = ClassModel::findOrFail($id);
        $class->students()->detach($request->student_ids);

        return redirect()->route('dashboard.kelas.show', $id)->with('success_siswa', 'Siswa berhasil dikeluarkan dari kelas.');
    }

    public function updateSchedule(Request $request, $id)
    {
        $scheduleMap = [
            'pagi' => ['05:00', '07:00', '15:00', '18:00'],
            'siang' => ['09:00', '11:00', '15:00', '18:00'],
        ];

        $scheduleType = $request->input('schedule_type');

        if ($scheduleType && isset($scheduleMap[$scheduleType])) {
            $times = $scheduleMap[$scheduleType];
            $request->merge([
                'start_time_in'  => $times[0],
                'end_time_in'    => $times[1],
                'start_time_out' => $times[2],
                'end_time_out'   => $times[3],
            ]);
        }

        $request->validate([
            'start_time_in'  => 'required|date_format:H:i',
            'end_time_in'    => 'required|date_format:H:i|after:start_time_in',
            'start_time_out' => 'required|date_format:H:i|after:end_time_in',
            'end_time_out'   => 'required|date_format:H:i|after:start_time_out',
            'schedule_type'   => 'nullable|in:pagi,siang',
        ], [
            'start_time_in.required'      => 'Waktu mulai masuk wajib diisi.',
            'start_time_in.date_format'   => 'Format waktu masuk tidak valid (HH:MM).',
            'end_time_in.after'           => 'Waktu batas masuk harus setelah waktu mulai masuk.',
            'start_time_out.after'        => 'Waktu mulai pulang harus setelah batas masuk.',
            'end_time_out.after'          => 'Waktu batas pulang harus setelah waktu mulai pulang.',
            'schedule_type.in'            => 'Jenis jadwal tidak valid.',
        ]);

        $class = ClassModel::findOrFail($id);

        $data = [
            'start_time_in'  => $request->start_time_in  . ':00',
            'end_time_in'    => $request->end_time_in    . ':00',
            'start_time_out' => $request->start_time_out . ':00',
            'end_time_out'   => $request->end_time_out   . ':00',
        ];

        $schedule = $class->attendanceSchedule;
        if ($schedule) {
            $schedule->update($data);
        } else {
            $class->attendanceSchedule()->create($data);
        }

        return redirect()->route('dashboard.kelas.show', $id)->with('success_jadwal', 'Jadwal berhasil diperbarui.');
    }

    public function updateName(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $class = ClassModel::findOrFail($id);
        $class->name = $request->name;
        $class->save();
        return redirect()->route('dashboard.kelas.show', $id)->with('success_nama_kelas', 'Nama kelas berhasil diubah.');
    }

    public function destroy(string $id)
    {
        $class = ClassModel::findOrFail($id);
        $class->students()->detach();
        $class->attendanceSchedule()?->delete();
        $class->subjects()->detach();
        $class->delete();
        return redirect()->route('dashboard.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
