@extends('dashboard.layout.app', ['title' => 'Halaman Detail Siswa'])

@section('content')
<div class="content-section mx-auto">
    <div class="backdrop-blur-sm rounded-xl px-6 mb-6 flex flex-row items-center justify-between">
        <div class="flex items-center gap-4">
            <i class="fas fa-user-graduate text-4xl text-blue-400"></i>
            <div>
                <h3 class="text-2xl font-semibold text-black">Detail Siswa</h3>
                <p class="text-slate-700">Halaman ini digunakan untuk melihat detail tiap-tiap siswa yang ada.</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.siswa') }}"><button type="button" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-2 rounded text-sm"><i class="fas fa-arrow-left"></i> Kembali</button></a>
            @php $hasDetail = $user->studentDetail !== null; @endphp
            <a href="{{ $hasDetail ? route('dashboard.siswa.detail.edit', $user->id) : route('dashboard.siswa.detail.create', $user->id) }}">
                <button type="button" class="bg-yellow-500 hover:bg-yellow-400 text-white px-3 py-2 rounded text-sm">
                    <i class="fas fa-edit"></i> {{ $hasDetail ? 'Edit Detail' : 'Tambah Detail' }}
                </button>
            </a>
        </div>
    </div>

    @php $detail = $user->studentDetail; @endphp
    <div class="bg-white backdrop-blur-sm rounded-xl p-6 border border-slate-700 mb-5">
        <div class="flex flex-row gap-5">
            <div>
                <p class="text-gray-700 text-lg">
                    @if($detail && $detail->photo)
                        <img src="{{ asset('storage/' . $detail->photo) }}" alt="Foto" class="w-48 h-48 object-cover rounded" />
                    @else
                        <span class="text-slate-400">-</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-col gap-2">
                <p class="text-gray-700 text-4xl font-semibold">{{ $user->name }}</p>
                <div class="flex flex-row gap-2">
                    <div class="bg-[#ecedf7] px-2 py-1 border border-gray-300 rounded-md text-sm"><span class="font-semibold text-slate-700">NIS: </span>{{$detail->nis ?? '-'}}</div>
                    <div class="bg-[#ecedf7] px-2 py-1 border border-gray-300 rounded-md text-sm"><span class="font-semibold text-slate-700">NISN: </span>{{$detail->nisn ?? '-'}}</div>
                    <div class="bg-[#ecedf7] px-2 py-1 border border-gray-300 rounded-md text-sm"><span class="font-semibold text-slate-700">Kelas: </span>{{ $studentClass?->name ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white backdrop-blur-sm rounded-xl p-6 border border-slate-700">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <h5 class="text-gray-700 text-xl font-medium mb-1">Nama</h5>
                <p class="text-gray-700 text-lg">{{ $user->name }}</p>
            </div>
            <div>
                <h5 class="text-gray-700 text-xl font-medium mb-1">Email</h5>
                <p class="text-gray-700 text-lg">{{ $user->email ?? '-' }}</p>
            </div>
            <div>
                <h5 class="text-gray-700 text-xl font-medium mb-1">Jenis Kelamin</h5>
                <p class="text-gray-700 text-lg">{{ $detail->gender ?? '-' }}</p>
            </div>
            <div>
                <h5 class="text-gray-700 text-xl font-medium mb-1">Foto</h5>
                <p class="text-gray-700 text-lg">
                    @if($detail && $detail->photo)
                        <img src="{{ asset('storage/' . $detail->photo) }}" alt="Foto" class="w-12 h-12 object-cover rounded" />
                    @else
                        <span class="text-slate-400">-</span>
                    @endif
                </p>
            </div>
            <div>
                <h5 class="text-gray-700 text-xl font-medium mb-1">NIS</h5>
                <p class="text-gray-700 text-lg">{{ $detail->nis ?? '-' }}</p>
            </div>
            <div>
                <h5 class="text-gray-700 text-xl font-medium mb-1">NISN</h5>
                <p class="text-gray-700 text-lg">{{ $detail->nisn ?? '-' }}</p>
            </div>
            <div>
                <h5 class="text-gray-700 text-xl font-medium mb-1">Tempat Lahir</h5>
                <p class="text-gray-700 text-lg">{{ $detail->birth_place ?? '-' }}</p>
            </div>
            <div>
                <h5 class="text-gray-700 text-xl font-medium mb-1">Tanggal Lahir</h5>
                <p class="text-gray-700 text-lg">{{ ($detail && $detail->birth_date) ? \Carbon\Carbon::parse($detail->birth_date)->format('d-m-Y') : '-' }}</p>
            </div>
            <div>
                <h5 class="text-gray-700 text-xl font-medium mb-1">Alamat</h5>
                <p class="text-gray-700 text-lg">{{ $detail->address ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
