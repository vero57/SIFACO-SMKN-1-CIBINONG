@extends('dashboard.layout.app', ['title' => 'Edit Mata Pelajaran'])

@section('content')
<div class="content-section mx-4">
    <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 px-10 border border-slate-700">
        <h3 class="text-3xl text-center font-semibold text-white mb-4">Edit Mata Pelajaran</h3>
        <form action="{{ route('dashboard.subjects.update', $classSubject->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-6 pb-4">
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Mata Pelajaran</label>
                    <select name="subject_id" required class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $classSubject->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="">
                    <label class="block text-slate-300 mb-1 text-sm">Kelas</label>
                    <select name="class_id" required class="w-full bg-slate-900 text-slate-200 border border-slate-700 rounded-lg px-3 py-3 text-sm">
                        <option value="">Pilih Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $classSubject->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
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
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $classSubject->teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="flex gap-6 w-full mt-6">
                <a href="{{ route('dashboard.subjects.index') }}" class="w-1/2 bg-slate-600 hover:bg-slate-500 text-white px-4 py-2 rounded-lg text-center font-semibold">Cancel</a>
                <button type="submit" class="w-1/2 bg-yellow-600 hover:bg-yellow-500 text-white px-4 py-2 rounded-lg font-semibold">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
