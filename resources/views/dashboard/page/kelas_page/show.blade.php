@extends('dashboard.layout.app', ['title' => 'Detail Kelas'])

@section('content')
<div class="content-section mx-auto">
    <div class="backdrop-blur-sm rounded-xl px-6 mb-6 flex flex-row items-center justify-between">
        <div class="flex items-center gap-4">
            <i class="fas fa-chalkboard-teacher text-4xl text-green-600"></i>
            <div>
                <h3 class="text-2xl font-semibold text-black">Detail Kelas</h3>
                <p class="text-slate-700">Halaman ini digunakan untuk melihat detail kelas dan siswa di dalamnya.</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.kelas.index') }}">
                <button type="button" class="bg-green-600 hover:bg-green-500 text-white px-3 py-2 rounded text-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
            </a>
        </div>
    </div>

    <div class="bg-white backdrop-blur-sm rounded-xl p-6 border border-gray-300 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h5 class="text-black text-xl font-medium mb-1">Nama Kelas</h5>
                <div class="flex items-center gap-2">
                    <p class="text-slate-800 text-lg">{{ $class->name }}</p>
                    <button
                        type="button"
                        onclick="document.getElementById('modalEditNamaKelas').classList.remove('hidden')"
                        class="ml-2 bg-yellow-600 hover:bg-yellow-500 text-white px-3 py-1 rounded text-xs font-semibold transition"
                        title="Ubah Nama Kelas"
                    >
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
                @if(session('success_nama_kelas'))
                    <div class="text-green-600 mt-2 text-sm">{{ session('success_nama_kelas') }}</div>
                @endif
            </div>
            <div>
                <h5 class="text-black text-xl font-medium mb-1">Wali Kelas</h5>
                <div>
                    <span class="text-slate-800 text-lg block">
                        {{ $class->walas ? $class->walas->name : '-' }}
                    </span>
                    <button
                        type="button"
                        onclick="document.getElementById('modalWalas').classList.remove('hidden')"
                        class="mt-3 bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded text-sm font-semibold transition"
                    >
                        <i class="fas fa-exchange-alt mr-1"></i> Ganti Wali Kelas
                    </button>
                </div>
                @if(session('success_walas'))
                    <div class="text-green-600 mt-2 text-sm">{{ session('success_walas') }}</div>
                @endif
            </div>
            <div>
                <h5 class="text-black text-xl font-medium mb-1">Jadwal</h5>
                @php
                    $schedule = $class->attendanceSchedule ?? null;
                @endphp
                <span class="text-slate-800 text-lg block">
                    @if($schedule)
                        @if(
                            $schedule->start_time_in == '05:00:00' &&
                            $schedule->end_time_in == '07:00:00' &&
                            $schedule->start_time_out == '15:00:00' &&
                            $schedule->end_time_out == '18:00:00'
                        )
                            Masuk Pagi: 06.30 - 16.00
                        @elseif(
                            $schedule->start_time_in == '09:00:00' &&
                            $schedule->end_time_in == '11:00:00' &&
                            $schedule->start_time_out == '15:00:00' &&
                            $schedule->end_time_out == '18:00:00'
                        )
                            Masuk Siang: 11.00 - 17.00
                        @else
                            {{ $schedule->start_time_in }} - {{ $schedule->end_time_in }} / {{ $schedule->start_time_out }} - {{ $schedule->end_time_out }}
                        @endif
                    @else
                        -
                    @endif
                </span>
                <button
                    type="button"
                    onclick="document.getElementById('modalJadwal').classList.remove('hidden')"
                    class="mt-3 bg-purple-600 hover:bg-purple-500 text-white px-4 py-2 rounded text-sm font-semibold transition"
                >
                    <i class="fas fa-clock mr-1"></i> Ubah Jadwal
                </button>
                @if(session('success_jadwal'))
                    <div class="text-green-600 mt-2 text-sm">{{ session('success_jadwal') }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white backdrop-blur-sm rounded-xl p-6 border border-gray-300">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h4 class="text-xl text-black font-semibold">Daftar Siswa Kelas</h4>
                <span class="text-slate-700 text-sm mt-1 block">
                    Jumlah siswa: <span class="text-black font-bold">{{ $class->students->count() }}</span>
                </span>
            </div>
            <div class="flex gap-2">
                <button
                    type="button"
                    onclick="document.getElementById('modalTambahSiswa').classList.remove('hidden')"
                    class="bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded text-sm font-semibold transition"
                >
                    <i class="fas fa-user-plus mr-1"></i> Tambah Siswa
                </button>
                <button
                    type="button"
                    onclick="document.getElementById('modalKeluarkanSiswa').classList.remove('hidden')"
                    class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded text-sm font-semibold transition"
                >
                    <i class="fas fa-user-minus mr-1"></i> Keluarkan Siswa
                </button>
            </div>
        </div>
        @if(session('success_siswa'))
            <div class="text-green-600 mb-4 text-sm">{{ session('success_siswa') }}</div>
        @endif
        <div class="overflow-x-auto -mx-4">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead>
                    <tr class="text-left text-black text-sm uppercase tracking-wider bg-[#ecedf7]">
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">NIS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                    @forelse($class->students as $student)
                        <tr class="hover:bg-[#ecedf7]">
                            <td class="px-4 py-3 text-slate-700 text-sm">{{ $student->name }}</td>
                            <td class="px-4 py-3 text-slate-700 text-sm">{{ $student->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700 text-sm">{{ $student->studentDetail->nis ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-slate-800 text-center">Tidak ada siswa di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Ganti Wali Kelas --}}
<div id="modalWalas" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
        <button onclick="document.getElementById('modalWalas').classList.add('hidden')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Pilih Wali Kelas</h3>
        @if(session('success_walas'))
            <div class="text-green-600 mb-2 text-sm">{{ session('success_walas') }}</div>
        @endif
        <form action="{{ route('dashboard.kelas.updateWalas', $class->id) }}" method="POST">
            @csrf
            <input type="text" id="searchGuru" placeholder="Cari nama guru..." class="w-full border border-gray-300 rounded px-3 py-2 mb-3" onkeyup="filterGuru()">
            <div class="max-h-60 overflow-y-auto border border-gray-200 rounded">
                <ul id="guruList">
                    @foreach($teachers as $teacher)
                        <li class="border-b last:border-b-0">
                            <label class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100">
                                <input type="radio" name="walas_id" value="{{ $teacher->id }}" class="mr-2" @if($class->walas && $class->walas->id == $teacher->id) checked @endif>
                                <span>{{ $teacher->name }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalWalas').classList.add('hidden')" class="px-4 py-2 rounded bg-gray-300 text-gray-700 hover:bg-gray-400">Batal</button>
                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-500">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Siswa --}}
<div id="modalTambahSiswa" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
        <button onclick="document.getElementById('modalTambahSiswa').classList.add('hidden')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Tambah Siswa ke Kelas</h3>
        <form action="{{ route('dashboard.kelas.addStudents', $class->id) }}" method="POST">
            @csrf
            <input type="text" id="searchSiswa" placeholder="Cari nama siswa..." class="w-full border border-gray-300 rounded px-3 py-2 mb-3" onkeyup="filterSiswa()">
            <div class="max-h-60 overflow-y-auto border border-gray-200 rounded">
                <ul id="siswaList">
                    @foreach($availableStudents as $student)
                        @php
                            $nis = $student->studentDetail->nis ?? null;
                            $nisn = $student->studentDetail->nisn ?? null;
                        @endphp
                        <li class="border-b last:border-b-0">
                            <label class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100">
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="mr-2">
                                <span>
                                    {{ $student->name }}
                                    @if($nis)
                                        - NIS: {{ $nis }}
                                    @endif
                                    @if($nisn)
                                        - NISN: {{ $nisn }}
                                    @endif
                                </span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalTambahSiswa').classList.add('hidden')" class="px-4 py-2 rounded bg-gray-300 text-gray-700 hover:bg-gray-400">Batal</button>
                <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-500">Tambah</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Keluarkan Siswa --}}
<div id="modalKeluarkanSiswa" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
        <button onclick="document.getElementById('modalKeluarkanSiswa').classList.add('hidden')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Keluarkan Siswa dari Kelas</h3>
        <form action="{{ route('dashboard.kelas.removeStudents', $class->id) }}" method="POST">
            @csrf
            <input type="text" id="searchKeluarkanSiswa" placeholder="Cari nama siswa..." class="w-full border border-gray-300 rounded px-3 py-2 mb-3" onkeyup="filterKeluarkanSiswa()">
            <div class="max-h-60 overflow-y-auto border border-gray-200 rounded">
                <ul id="keluarkanSiswaList">
                    @foreach($class->students as $student)
                        @php
                            $nis = $student->studentDetail->nis ?? null;
                            $nisn = $student->studentDetail->nisn ?? null;
                        @endphp
                        <li class="border-b last:border-b-0">
                            <label class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100">
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="mr-2">
                                <span>
                                    {{ $student->name }}
                                    @if($nis)
                                        - NIS: {{ $nis }}
                                    @endif
                                    @if($nisn)
                                        - NISN: {{ $nisn }}
                                    @endif
                                </span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalKeluarkanSiswa').classList.add('hidden')" class="px-4 py-2 rounded bg-gray-300 text-gray-700 hover:bg-gray-400">Batal</button>
                <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-500">Keluarkan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Ubah Jadwal --}}
<div id="modalJadwal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <button onclick="document.getElementById('modalJadwal').classList.add('hidden')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Ubah Jadwal Kelas</h3>
        @if(session('success_jadwal'))
            <div class="text-green-600 mb-2 text-sm">{{ session('success_jadwal') }}</div>
        @endif
        <form action="{{ route('dashboard.kelas.updateSchedule', $class->id) }}" method="POST">
            @csrf
            <div class="space-y-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="schedule_type" value="pagi"
                        @if(isset($schedule) && $schedule->start_time_in == '05:00:00' && $schedule->end_time_in == '07:00:00' && $schedule->start_time_out == '15:00:00' && $schedule->end_time_out == '18:00:00') checked @endif>
                    <span>
                        Masuk Pagi: 06.30 - 16.00
                        <span class="block text-xs text-gray-500">In: 05:00 - 07:00, Out: 15:00 - 18:00</span>
                    </span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="schedule_type" value="siang"
                        @if(isset($schedule) && $schedule->start_time_in == '09:00:00' && $schedule->end_time_in == '11:00:00' && $schedule->start_time_out == '15:00:00' && $schedule->end_time_out == '18:00:00') checked @endif>
                    <span>
                        Masuk Siang: 11.00 - 17.00
                        <span class="block text-xs text-gray-500">In: 09:00 - 11:00, Out: 15:00 - 18:00</span>
                    </span>
                </label>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalJadwal').classList.add('hidden')" class="px-4 py-2 rounded bg-gray-300 text-gray-700 hover:bg-gray-400">Batal</button>
                <button type="submit" class="px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-500">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Ubah Nama Kelas --}}
<div id="modalEditNamaKelas" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <button onclick="document.getElementById('modalEditNamaKelas').classList.add('hidden')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Ubah Nama Kelas</h3>
        @if(session('success_nama_kelas'))
            <div class="text-green-600 mb-2 text-sm">{{ session('success_nama_kelas') }}</div>
        @endif
        <form action="{{ route('dashboard.kelas.updateName', $class->id) }}" method="POST">
            @csrf
            <input type="text" name="name" value="{{ $class->name }}" class="w-full border border-gray-300 rounded px-3 py-2 mb-3" required>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalEditNamaKelas').classList.add('hidden')" class="px-4 py-2 rounded bg-gray-300 text-gray-700 hover:bg-gray-400">Batal</button>
                <button type="submit" class="px-4 py-2 rounded bg-yellow-600 text-white hover:bg-yellow-500">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function filterGuru() {
    var input = document.getElementById('searchGuru');
    var filter = input.value.toLowerCase();
    var ul = document.getElementById('guruList');
    var li = ul.getElementsByTagName('li');
    for (var i = 0; i < li.length; i++) {
        var label = li[i].getElementsByTagName("label")[0];
        var txtValue = label.textContent || label.innerText;
        li[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
    }
}
function filterSiswa() {
    var input = document.getElementById('searchSiswa');
    var filter = input.value.toLowerCase();
    var ul = document.getElementById('siswaList');
    var li = ul.getElementsByTagName('li');
    for (var i = 0; i < li.length; i++) {
        var label = li[i].getElementsByTagName("label")[0];
        var txtValue = label.textContent || label.innerText;
        li[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
    }
}
function filterKeluarkanSiswa() {
    var input = document.getElementById('searchKeluarkanSiswa');
    var filter = input.value.toLowerCase();
    var ul = document.getElementById('keluarkanSiswaList');
    var li = ul.getElementsByTagName('li');
    for (var i = 0; i < li.length; i++) {
        var label = li[i].getElementsByTagName("label")[0];
        var txtValue = label.textContent || label.innerText;
        li[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
    }
}
</script>
@endsection
