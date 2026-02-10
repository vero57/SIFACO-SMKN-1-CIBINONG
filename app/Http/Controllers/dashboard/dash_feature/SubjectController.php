<?php

namespace App\Http\Controllers\dashboard\dash_feature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassSubject;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\User;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        // Check if it's an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return $this->searchAjax($request);
        }

        $user = auth()->user();
        $search = $request->input('search');
        if ($user && $user->role && $user->role->name === 'Guru') {
            // Ambil id kelas di mana dia wali kelas
            $walasClassIds = \App\Models\ClassModel::where('walas_id', $user->id)->pluck('id')->toArray();
            // Ambil id class_subject di mana dia sebagai guru mapel
            $subjects = \App\Models\ClassSubject::with(['subject', 'class', 'teacher'])
                ->where(function ($q) use ($user, $walasClassIds) {
                    $q->where('teacher_id', $user->id);
                    if (!empty($walasClassIds)) {
                        $q->orWhereIn('class_id', $walasClassIds);
                    }
                })
                ->when($search, function ($query) use ($search) {
                    $query->whereHas('subject', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                        ->orWhereHas('class', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('teacher', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->orderBy('id')
                ->paginate(10)
                ->appends(['search' => $search]);
        } else {
            $subjects = \App\Models\ClassSubject::with(['subject', 'class', 'teacher'])
                ->when($search, function ($query) use ($search) {
                    $query->whereHas('subject', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                        ->orWhereHas('class', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('teacher', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->orderBy('id')
                ->paginate(10)
                ->appends(['search' => $search]);
        }
        return view('dashboard.page.subject_page.index', compact('subjects', 'search'));
    }

    /**
     * Search mata pelajaran via AJAX
     */
    public function searchAjax(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');

        if ($user && $user->role && $user->role->name === 'Guru') {
            // Ambil id kelas di mana dia wali kelas
            $walasClassIds = \App\Models\ClassModel::where('walas_id', $user->id)->pluck('id')->toArray();
            // Ambil id class_subject di mana dia sebagai guru mapel
            $subjects = \App\Models\ClassSubject::with(['subject', 'class', 'teacher'])
                ->where(function ($q) use ($user, $walasClassIds) {
                    $q->where('teacher_id', $user->id);
                    if (!empty($walasClassIds)) {
                        $q->orWhereIn('class_id', $walasClassIds);
                    }
                })
                ->when($search, function ($query) use ($search) {
                    $query->whereHas('subject', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                        ->orWhereHas('class', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('teacher', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->orderBy('id')
                ->get();
        } else {
            $subjects = \App\Models\ClassSubject::with(['subject', 'class', 'teacher'])
                ->when($search, function ($query) use ($search) {
                    $query->whereHas('subject', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                        ->orWhereHas('class', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('teacher', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->orderBy('id')
                ->get();
        }

        return response()->json([
            'data' => $subjects,
            'info' => "Ditemukan {$subjects->count()} mata pelajaran"
        ]);
    }

    public function create()
    {
        // Ambil semua subject untuk pilihan
        $subjects = Subject::orderBy('name')->get();
        $classes = ClassModel::orderBy('name')->get();
        $teachers = User::whereHas('role', function ($q) {
            $q->where('name', 'Guru');
        })->orderBy('name')->get();
        return view('dashboard.page.subject_page.create', compact('subjects', 'classes', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
            'new_subject_name' => 'nullable|string|max:255',
            // subject_id hanya divalidasi jika bukan __new__
            'subject_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== null && $value !== '__new__' && !\App\Models\Subject::find($value)) {
                        $fail('Mata pelajaran yang dipilih tidak valid.');
                    }
                }
            ],
        ]);

        // Jika user memilih tambah baru, ambil dari input
        if ($request->subject_id === '__new__' && $request->filled('new_subject_name')) {
            $subject = \App\Models\Subject::create([
                'name' => $request->new_subject_name
            ]);
            $subject_id = $subject->id;
        } else {
            $subject_id = $request->subject_id;
        }

        // Pastikan subject_id sudah terisi
        if (!$subject_id) {
            return back()->withErrors(['subject_id' => 'Mata pelajaran harus dipilih atau ditambahkan.'])->withInput();
        }

        ClassSubject::create([
            'subject_id' => $subject_id,
            'class_id' => $request->class_id,
            'teacher_id' => $request->teacher_id
        ]);
        return redirect()->route('dashboard.subjects.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $classSubject = ClassSubject::findOrFail($id);
        $subjects = Subject::orderBy('name')->get();
        $classes = ClassModel::orderBy('name')->get();
        $teachers = User::whereHas('role', function ($q) {
            $q->where('name', 'Guru');
        })->orderBy('name')->get();
        return view('dashboard.page.subject_page.edit', compact('classSubject', 'subjects', 'classes', 'teachers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id'
        ]);
        $classSubject = ClassSubject::findOrFail($id);
        $classSubject->update($request->only('subject_id', 'class_id', 'teacher_id'));
        return redirect()->route('dashboard.subjects.index')->with('success', 'Mata pelajaran berhasil diupdate.');
    }

    public function destroy($id)
    {
        $classSubject = ClassSubject::findOrFail($id);
        $classSubject->delete();
        return redirect()->route('dashboard.subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
