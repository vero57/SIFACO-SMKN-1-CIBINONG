<?php

namespace App\Http\Controllers\dashboard\dash_feature;

use App\Http\Controllers\Controller;
use App\Exports\JournalExport;
use Illuminate\Http\Request;
use App\Models\Journal;
use Maatwebsite\Excel\Facades\Excel;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        // Check if it's an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return $this->searchAjax($request);
        }

        $search = $request->input('search');
        $journals = Journal::with(['student', 'subject'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('subject', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('dashboard.page.jurnal_page.index', compact('journals', 'search'));
    }

    /**
     * Search jurnal via AJAX
     */
    public function searchAjax(Request $request)
    {
        $search = $request->input('search');
        $journals = Journal::with(['student', 'subject'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('subject', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->get();

        return response()->json([
            'data' => $journals,
            'info' => "Ditemukan {$journals->count()} jurnal"
        ]);
    }

    public function show($id)
    {
        $journal = Journal::with(['student', 'subject', 'files'])->findOrFail($id);
        return view('dashboard.page.jurnal_page.show', compact('journal'));
    }

    public function exportExcel()
    {
        $journals = Journal::with(['student', 'subject'])->get();

        return Excel::download(new JournalExport($journals), 'daftar_jurnal_siswa.xlsx');
    }
}
