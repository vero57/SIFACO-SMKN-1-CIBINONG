<?php

namespace App\Http\Controllers\dashboard\dash_feature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StudentDetail;
use Illuminate\Support\Facades\Storage;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        // Check if it's an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return $this->searchAjax($request);
        }

        $search = $request->input('search');
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'Siswa');
        })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhereHas('studentDetail', function ($q2) use ($search) {
                            $q2->where('nis', 'like', '%' . $search . '%')
                                ->orWhere('nisn', 'like', '%' . $search . '%');
                        });
                });
            })
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('dashboard.page.siswa_page.index', compact('users', 'search'));
    }

    /**
     * Search siswa via AJAX
     */
    public function searchAjax(Request $request)
    {
        $search = $request->input('search');
        $users = User::with('studentDetail')
            ->whereHas('role', function ($q) {
                $q->where('name', 'Siswa');
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhereHas('studentDetail', function ($q2) use ($search) {
                            $q2->where('nis', 'like', '%' . $search . '%')
                                ->orWhere('nisn', 'like', '%' . $search . '%');
                        });
                });
            })
            ->get();

        return response()->json([
            'data' => $users,
            'info' => "Ditemukan {$users->count()} siswa"
        ]);
    }

    public function createDetail($user_id)
    {
        $user = User::findOrFail($user_id);
        $detail = $user->studentDetail;
        if ($detail) {
            // Jika detail sudah ada, arahkan ke halaman edit
            return redirect()->route('dashboard.siswa.detail.edit', $user->id);
        }
        return view('dashboard.page.siswa_page.edit', compact('user'));
    }

    public function storeDetail(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);
        $detail = $user->studentDetail;

        $rules = [
            'nis' => 'required|string|max:20|unique:student_details,nis,' . ($detail ? $detail->id : 'NULL'),
            'nisn' => 'nullable|string|max:20|unique:student_details,nisn,' . ($detail ? $detail->id : 'NULL'),
            'gender' => 'required|in:L,P',
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'address' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ];
        $request->validate($rules);

        $data = $request->only(['nis', 'nisn', 'gender', 'birth_place', 'birth_date', 'address']);
        $data['user_id'] = $user->id;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('student_photos', 'public');
        }

        if ($detail) {
            // Update detail jika sudah ada
            $detail->update($data);
            return redirect()->route('dashboard.siswa')->with('success', 'Detail siswa berhasil diupdate!');
        } else {
            StudentDetail::create($data);
            return redirect()->route('dashboard.siswa')->with('success', 'Detail siswa berhasil ditambahkan!');
        }
    }

    public function show($user_id)
    {
        $user = User::with(['studentDetail', 'classes'])->findOrFail($user_id);
        $studentClass = $user->classes->first();

        return view('dashboard.page.siswa_page.show', compact('user', 'studentClass'));
    }

    public function editDetail($user_id)
    {
        $user = User::findOrFail($user_id);
        $detail = $user->studentDetail;
        if (!$detail) {
            // Jika belum ada detail, arahkan ke tambah
            return redirect()->route('dashboard.siswa.detail.create', $user->id);
        }
        return view('dashboard.page.siswa_page.edit', compact('user', 'detail'));
    }

    public function updateDetail(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);
        $detail = $user->studentDetail;
        if (!$detail) {
            return redirect()->route('dashboard.siswa.detail.create', $user->id);
        }

        $rules = [
            'nis' => 'required|string|max:20|unique:student_details,nis,' . $detail->id,
            'nisn' => 'nullable|string|max:20|unique:student_details,nisn,' . $detail->id,
            'gender' => 'required|in:L,P',
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'address' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ];
        $request->validate($rules);

        $data = $request->only(['nis', 'nisn', 'gender', 'birth_place', 'birth_date', 'address']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('student_photos', 'public');
        }

        $detail->update($data);

        return redirect()->route('dashboard.siswa')->with('success', 'Detail siswa berhasil diupdate!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new UsersImport, $request->file('file'));

        return redirect()->route('dashboard.siswa')->with('success', 'Import data siswa berhasil!');
    }
}
