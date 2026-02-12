@extends('dashboard.layout.app', ['title' => 'Create User'])

@section('content')
<div class="content-section mx-4">
    <div class="w-full bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 px-10 border border-slate-700">
        <h3 class="text-3xl font-semibold text-white mb-4 text-center">Tambah User</h3>
        <form action="{{ route('dashboard.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-2 gap-6 pb-4">
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Nama User</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                </div>
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">No HP</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                </div>
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Role</label>
                    <select name="roles[]" required class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled selected>-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('roles.0') == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                </div>
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Password</label>
                    <input type="password" name="password" required class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                </div>
            </div>
            <div class="flex gap-6 mt-6 w-full">
                <a href="{{ route('dashboard.users.index') }}" class="w-1/2 bg-slate-600 hover:bg-slate-500 text-white px-4 py-2 rounded-lg text font-semibold text-center">Cancel</a>
                <button type="submit" class="w-1/2 bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-lg text font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
