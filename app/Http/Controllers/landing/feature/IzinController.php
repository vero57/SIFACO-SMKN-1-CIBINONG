<?php

namespace App\Http\Controllers\landing\feature;

use App\Events\PermissionCreated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\PermissionFile;
use App\Models\User;
use App\Models\ParentModel;
use App\Models\Role;

class IzinController extends Controller
{
    public function index()
    {
        return view('landing.feature.izin.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|max:255',
            'parent_name' => 'required|string|max:255',
            'type' => 'required|in:sakit,izin,dispen',
            'description' => 'required|string',
            'status' => 'in:pending,approved,rejected',
        ]);

        // Simpan foto dari kamera jika ada
        $photoPath = null;
        if ($request->photo && preg_match('/^data:image\/(\w+);base64,/', $request->photo, $type)) {
            $base64 = substr($request->photo, strpos($request->photo, ',') + 1);
            $type = strtolower($type[1]);
            $base64 = base64_decode($base64);
            $filename = 'izin_' . $request->student_id . '_' . time() . '.' . $type;
            $path = 'izin_photos/' . $filename;
            \Storage::disk('public')->put($path, $base64);
            $photoPath = 'storage/' . $path;
        }

        $permission = Permission::create([
            'student_id' => $request->student_id,
            'parent_name' => $request->parent_name,
            'type' => $request->type,
            'description' => $request->description,
            'photo' => $photoPath,
            'location_lat' => $request->location_lat,
            'location_lng' => $request->location_lng,
        ]);

        $permission->save();

        // Multiple file support
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $filePath = $file->store('permissions', 'public');
                PermissionFile::create([
                    'permission_id' => $permission->id,
                    'file_path' => $filePath
                ]);
            }
        }

        event(new PermissionCreated($permission));

        return redirect()->route('landing.home')->with('success', 'Izin berhasil diajukan.');
    }

    public function face()
    {
        return view('landing.feature.izin.face');
    }
}
