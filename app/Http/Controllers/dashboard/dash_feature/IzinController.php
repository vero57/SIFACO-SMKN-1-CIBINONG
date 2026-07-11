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
        // Check if it's an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return $this->searchAjax($request);
        }

        $search = $request->input('search');
        $permissions = Permission::with(['student'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            })
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('dashboard.page.izin_page.index', compact('permissions', 'search'));
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
        $permission = Permission::with(['student'])->findOrFail($id);
        return view('dashboard.page.izin_page.show', compact('permission'));
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
