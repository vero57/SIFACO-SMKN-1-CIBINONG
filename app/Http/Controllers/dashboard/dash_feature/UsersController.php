<?php

namespace App\Http\Controllers\dashboard\dash_feature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if it's an AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return $this->searchAjax();
        }

        $query = User::with('role');
        if (request()->has('search') && request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $users = $query->paginate(10)->appends(['search' => request('search')]);
        return view('dashboard.page.users_page.index', compact('users'));
    }

    /**
     * Search users via AJAX
     */
    public function searchAjax()
    {
        $query = User::with('role');

        if (request()->has('search') && request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        return response()->json([
            'data' => $users,
            'info' => "Ditemukan {$users->count()} user"
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('dashboard.page.users_page.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'roles' => 'required|array',
            'roles.0' => 'required|exists:roles,id',
            'password' => 'required|string|min:6',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->role_id = $request->roles[0];
        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()->route('dashboard.users.index')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('dashboard.page.users_page.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'roles' => 'required|array',
            'roles.0' => 'required|exists:roles,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->role_id = $request->roles[0];
        $user->save();

        return redirect()->route('dashboard.users.index')->with('success', 'User updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('dashboard.users.index')->with('success', 'User deleted!');
    }
}
