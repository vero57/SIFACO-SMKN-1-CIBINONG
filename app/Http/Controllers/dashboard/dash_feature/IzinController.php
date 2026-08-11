<?php

namespace App\Http\Controllers\dashboard\dash_feature;

use App\Http\Controllers\Controller;
use App\Exports\PermissionExport;
use Illuminate\Http\Request;
use App\Models\Permission;
use Maatwebsite\Excel\Facades\Excel;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array((int) $perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $permissions = Permission::with(['student'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            })
            ->paginate($perPage)
            ->appends([
                'search' => $search,
                'per_page' => $perPage,
            ]);

        return view('dashboard.page.izin_page.index', compact('permissions', 'search', 'perPage'));
    }

    /**
     * Search izin via AJAX
     */
    public function searchAjax(Request $request)
    {
        $search = $request->input('search');
        $permissions = Permission::with(['student'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            })
            ->get();

        return response()->json([
            'data' => $permissions,
            'info' => "Ditemukan {$permissions->count()} izin"
        ]);
    }

    public function show($id)
    {
        $permission = Permission::with(['student.classes', 'student.studentDetail'])->findOrFail($id);
        $studentClass = $permission->student ? $permission->student->classes->first() : null;

        return view('dashboard.page.izin_page.show', compact('permission', 'studentClass'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status izin berhasil diupdate.');
    }

    public function exportExcel()
    {
        $permissions = Permission::with(['student'])->get();

        return Excel::download(new PermissionExport($permissions), 'daftar_izin_siswa.xlsx');
    }
}
