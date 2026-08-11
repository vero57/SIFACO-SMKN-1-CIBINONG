@extends(
    "dashboard.layout.app",
    [
        "title" => "Detail Jurnal Siswa",
    ]
)

@section('content')
<div class="content-section mx-auto">
    <div class="backdrop-blur-sm rounded-xl px-6 mb-6 flex flex-row items-center justify-between">
        <div class="flex items-center gap-4">
            <i class="fas fa-book-open text-4xl text-purple-400"></i>
            <div>
                <h3 class="text-2xl font-semibold text-black">Detail Jurnal Siswa</h3>
                <p class="text-slate-700">Halaman ini digunakan untuk melihat detail daftar jurnal yang dikumpulkan siswa — nama, mata pelajaran, dan ringkasan deskripsi.</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.jurnal') }}"><button type="button" class="bg-purple-600 hover:bg-purple-500 text-white px-3 py-2 rounded text-sm"><i class="fas fa-arrow-left"></i> Kembali</button></a>
        </div>
    </div>

    <div class="bg-white backdrop-blur-sm rounded-xl p-6 border border-gray-300">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <h5 class="text-black text-xl font-medium mb-1">Nama</h5>
                <p class="text-slate-700 text-lg">{{ $journal->student->name ?? 'Nama Tidak Ditemukan' }}</p>
            </div>

            <div>
                <h5 class="text-black text-xl font-medium mb-1">Kelas</h5>
                <p class="text-slate-700 text-lg">{{ $journal->student->classes->first()->name ?? '-' }}</p>
            </div>

            <div>
                <h5 class="text-black text-xl font-medium mb-1">NIS</h5>
                <p class="text-slate-700 text-lg">{{ $journal->student->studentDetail->nis ?? '-' }}</p>
            </div>

            <div>
                <h5 class="text-black text-xl font-medium mb-1">NISN</h5>
                <p class="text-slate-700 text-lg">{{ $journal->student->studentDetail->nisn ?? '-' }}</p>
            </div>

            <div>
                <h5 class="text-black text-xl font-medium mb-1">Tanggal</h5>
                <p class="text-slate-700 text-lg">{{ $journal->created_at->format('Y-m-d') }}</p>
            </div>

            <div>
                <h5 class="text-black text-xl font-medium mb-1">Pelajaran</h5>
                <p class="text-slate-700 text-lg">{{ $journal->subject->name ?? 'Pelajaran Tidak Ditemukan' }}</p>
            </div>

            <div class="col-span-2">
                <h5 class="text-black text-xl font-medium mb-1">Foto</h5>
                @if($journal->files->count() > 0)
                    @foreach($journal->files as $file)
                        <img src="{{ asset('storage/' . $file->file_path) }}" alt="Foto Jurnal" class="w-32 h-32 object-cover rounded cursor-pointer hover:opacity-80" onclick="openModal('{{ asset('storage/' . $file->file_path) }}')">
                    @endforeach
                @else
                    <p class="text-slate-400">Tidak ada foto</p>
                @endif
            </div>

            <div class="col-span-2">
                <h5 class="text-black text-xl font-medium mb-1">Deskripsi</h5>
                <p class="text-slate-700 text-lg">{{ $journal->description }}</p>
            </div>
        </div>
    </div>

    <div id="imageModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="relative">
            <img id="modalImage" src="" alt="Zoomed Image" class="max-w-full max-h-full">
            <button onclick="closeModal()" class="absolute top-2 right-2 bg-white text-black border border-white px-1 text-2xl">&times;</button>
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
