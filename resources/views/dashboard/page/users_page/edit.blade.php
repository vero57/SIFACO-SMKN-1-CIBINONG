@extends('dashboard.layout.app', ['title' => 'Edit User'])

@section('content')
<div class="content-section mx-4">
    <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 px-10 border border-slate-700">
        <h3 class="text-3xl font-semibold text-white mb-4 text-center">Edit User</h3>
        <form action="{{ route('dashboard.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-6 pb-4">
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Nama User</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                </div>
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                </div>
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">No HP</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                </div>
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Role</label>
                    <select name="roles[]" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled {{ empty($user->role_id) ? 'selected' : '' }}>-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}"
                                @if($user->role_id == $role->id) selected @endif>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="w-full flex gap-6 mt-6">
                <a href="{{ route('dashboard.users.index') }}" class="w-1/2 bg-slate-600 hover:bg-slate-500 text-white px-4 py-2 rounded-lg text font-semibold text-center">Cancel</a>
                <button type="submit" class="w-1/2 bg-yellow-600 hover:bg-yellow-500 text-white px-4 py-2 rounded-lg text font-semibold">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
