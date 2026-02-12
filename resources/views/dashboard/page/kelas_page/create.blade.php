@extends('dashboard.layout.app', ['title' => 'Tambah Kelas'])

@section('content')
@php
    $role = auth()->check() && auth()->user()->role ? auth()->user()->role->name : null;
@endphp
@if($role === 'Admin')
<div class="content-section mx-4">
    <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 px-10 border border-slate-700">
        <h3 class="text-3xl text-center font-semibold text-white mb-4">Tambah Kelas</h3>
        <form action="{{ route('dashboard.kelas.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-6 pb-4">

                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Nama Kelas</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm" placeholder="Nama Kelas">
                    @error('name')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Wali Kelas</label>
                    <select name="walas_id" required class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm">
                        <option value="">Pilih Wali Kelas</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('walas_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('walas_id')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="flex gap-6 w-full mt-6">
                <a href="{{ route('dashboard.kelas.index') }}" class="w-1/2 bg-slate-600 hover:bg-slate-500 text-white px-4 py-2 rounded-lg font-semibold text-center">Cancel</a>
                <button type="submit" class="w-1/2 bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-lg font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            const errors = {
                @foreach($errors->all() as $error)
                    '{{ $loop->index }}': '{{ $error }}',
                @endforeach
            };
            showValidationErrors(errors, 'Validasi Input Gagal');
        });
    @endif

    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            showSuccess('{{ session("success") }}', 'Berhasil!');
        });
    @endif
</script>
@else
<div class="content-section max-w-xl mx-auto">
    <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700 text-center text-slate-300">
        Anda tidak memiliki akses untuk menambah Kelas.
    </div>
</div>
@endif
@endsection
