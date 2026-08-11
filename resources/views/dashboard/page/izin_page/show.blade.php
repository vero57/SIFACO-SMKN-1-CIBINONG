@extends(
    "dashboard.layout.app",
    [
        "title" => "Detail Izin Siswa",
    ]
)

@section('content')
<div class="content-section mx-auto">
    <div class="backdrop-blur-sm rounded-xl px-6 mb-6 flex flex-row items-center justify-between">
        <div class="flex items-center gap-4">
            <i class="fas fa-file-text text-4xl text-gray-400"></i>
            <div>
                <h3 class="text-2xl font-semibold text-black">Detail Pengajuan Izin</h3>
                <p class="text-slate-700">Halaman ini digunakan untuk melihat detail daftar izin siswa — tanggal, jam, status, dan lokasi.</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.izin') }}"><button type="button" class="bg-gray-600 hover:bg-gray-500 text-white px-3 py-2 rounded text-sm"><i class="fas fa-arrow-left"></i> Kembali</button></a>
        </div>
    </div>

    <div class="bg-white backdrop-blur-sm rounded-xl p-6 border border-gray-300 mb-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <h5 class="text-black text-xl font-medium mb-1">Nama</h5>
                <p class="text-slate-700 text-lg">{{ $permission->student->name ?? '-' }}</p>
            </div>

            <div>
                <h5 class="text-black text-xl font-medium mb-1">Kelas</h5>
                <p class="text-slate-700 text-lg">{{ $studentClass->name ?? '-' }}</p>
            </div>

            <div>
                <h5 class="text-black text-xl font-medium mb-1">NIS</h5>
                <p class="text-slate-700 text-lg">{{ $permission->student->studentDetail->nis ?? '-' }}</p>
            </div>

            <div>
                <h5 class="text-black text-xl font-medium mb-1">NISN</h5>
                <p class="text-slate-700 text-lg">{{ $permission->student->studentDetail->nisn ?? '-' }}</p>
            </div>

            <div>
                <h5 class="text-black text-xl font-medium mb-1">Nama Orang Tua</h5>
                <p class="text-slate-700 text-lg">{{ $permission->parent_name ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white backdrop-blur-sm rounded-xl p-6 border border-gray-300">
        <div class="grid grid-cols-2 gap-6">

            <div>
                <h5 class="text-black text-xl font-medium mb-1">Tanggal</h5>
                <p class="text-slate-700 text-lg">{{ $permission->created_at->format('Y-m-d') ?? '-' }}</p>
            </div>

            <div>
                <h5 class="text-black text-xl font-medium mb-1">Jenis Izin</h5>
                <p class="text-slate-700 text-lg">{{ $permission->type ?? '-' }}</p>
            </div>

            <div>
                <h5 class="text-black text-xl font-medium mb-1">Status</h5>
                <p class="text-slate-700 text-lg">{{ $permission->status ?? '-' }}</p>
            </div>

            <div>
                <h5 class="text-black text-xl font-medium mb-1">Lokasi</h5>
                @if($permission->location_lat && $permission->location_lng)
                    <p class="text-slate-700 text-lg">
                        {{ $permission->location_lat }}, {{ $permission->location_lng }}
                        <a href="https://maps.google.com/?q={{ $permission->location_lat }},{{ $permission->location_lng }}" target="_blank" class="ml-2 text-blue-400 underline">Lihat di Maps</a>
                    </p>
                @else
                    <p class="text-slate-400">Tidak ada lokasi</p>
                @endif
            </div>

            <div class="col-span-2">
                <h5 class="text-black text-xl font-medium mb-1">Foto Selfie</h5>
                @if($permission->photo)
                    <img src="{{ asset($permission->photo) }}" alt="Foto Selfie" class="w-32 h-32 object-cover rounded cursor-pointer hover:opacity-80 mb-2" onclick="openModal('{{ asset($permission->photo) }}')">
                @else
                    <p class="text-slate-400">Tidak ada foto selfie</p>
                @endif
            </div>

            <div class="col-span-2">
                <h5 class="text-black text-xl font-medium mb-1">Lampiran File</h5>
                @if($permission->files->count() > 0)
                    <div class="flex flex-wrap gap-4">
                        @foreach($permission->files as $file)
                            @php
                                $ext = pathinfo($file->file_path, PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($ext), ['jpg','jpeg','png','gif','bmp','webp']);
                            @endphp
                            @if($isImage)
                                <img src="{{ asset('storage/' . $file->file_path) }}" alt="Lampiran" class="w-32 h-32 object-cover rounded cursor-pointer hover:opacity-80" onclick="openModal('{{ asset('storage/' . $file->file_path) }}')">
                            @else
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="block text-blue-400 underline">{{ basename($file->file_path) }}</a>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-400">Tidak ada lampiran file</p>
                @endif
            </div>

            <div class="col-span-2">
                <h5 class="text-black text-xl font-medium mb-1">Deskripsi</h5>
                <p class="text-slate-700 text-lg">{{ $permission->description }}</p>
            </div>
        </div>
    </div>

    <div id="imageModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="relative">
            <img id="modalImage" src="" alt="Zoomed Image" class="max-w-full max-h-full">
            <button onclick="closeModal()" class="absolute top-2 right-2 text-black bg-white border border-white px-1 text-2xl">&times;</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('imageModal').classList.add('hidden');
}
</script>
@endpush
