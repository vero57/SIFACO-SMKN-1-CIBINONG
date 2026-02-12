@extends('dashboard.layout.app', ['title' => 'Tambah Kelas'])

@section('content')
@php
    $role = auth()->check() && auth()->user()->role ? auth()->user()->role->name : null;
@endphp
@if($role === 'Admin')
<div class="content-section mx-4">
    <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 px-10 border border-slate-700">
        <h3 class="text-3xl text-center font-semibold text-white mb-4">Tambah Kelas</h3>
        <form action="{{ route('dashboard.subjects.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-6 pb-4">
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Mata Pelajaran</label>
                    <select name="subject_id" id="subject_id" required class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                        <option value="__new__" {{ old('subject_id') == '__new__' ? 'selected' : '' }}>+ Tambah Mata Pelajaran Baru</option>
                    </select>
                    @error('subject_id')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                    <div id="new-subject-field" class="mt-2" style="display: none;">
                        <input type="text" name="new_subject_name" value="{{ old('new_subject_name') }}" class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm" placeholder="Nama Mata Pelajaran Baru" />
                        @error('new_subject_name')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Kelas</label>
                    <select name="class_id" required class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm">
                        <option value="">Pilih Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Guru</label>
                    <select name="teacher_id" required class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm">
                        <option value="">Pilih Guru</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="flex gap-6 mt-6 w-full">
                <a href="{{ route('dashboard.subjects.index') }}" class="w-1/2 bg-slate-600 hover:bg-slate-500 text-white px-4 py-2 rounded-lg font-semibold text-center">Cancel</a>
                <button type="submit" class="w-1/2 bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-lg font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var select = document.getElementById('subject_id');
        var newField = document.getElementById('new-subject-field');
        function toggleNewField() {
            if (select.value === '__new__') {
                newField.style.display = '';
            } else {
                newField.style.display = 'none';
            }
        }
        select.addEventListener('change', toggleNewField);
        toggleNewField();
    });
</script>
@else
<div class="content-section max-w-xl mx-auto">
    <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700 text-center text-slate-300">
        Anda tidak memiliki akses untuk menambah Mata Pelajaran.
    </div>
</div>
@endif
@endsection
