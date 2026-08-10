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
            <i class="fas fa-exclamation-triangle text-4xl text-red-400"></i>
            <div>
                <h3 class="text-2xl font-semibold text-black">Detail Pelanggaran Siswa</h3>
                <p class="text-slate-700">Halaman ini digunakan untuk melihat detail daftar pelanggaran siswa — tanggal, jam, status, dan lokasi.</p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard.pelanggaran') }}"><button type="button" class="bg-red-600 hover:bg-red-500 text-white px-3 py-2 rounded text-sm"><i class="fas fa-arrow-left"></i> Kembali</button></a>
        </div>
    </div>

    <div class="bg-white backdrop-blur-sm rounded-xl p-6 border border-gray-300">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <h5 class="text-black text-xl font-medium mb-1">Nama</h5>
                <p class="text-slate-700 text-lg">{{ $violation->student->name ?? '-' }}</p>
            </div>
            <div>
                <h5 class="text-black text-xl font-medium mb-1">Kelas</h5>
                <p class="text-slate-700 text-lg">{{ $violation->student->classes->first()->name ?? '-' }}</p>
            </div>
            <div>
                <h5 class="text-black text-xl font-medium mb-1">NIS</h5>
                <p class="text-slate-700 text-lg">{{ $violation->student->studentDetail->nis ?? '-' }}</p>
            </div>
            <div>
                <h5 class="text-black text-xl font-medium mb-1">NISN</h5>
                <p class="text-slate-700 text-lg">{{ $violation->student->studentDetail->nisn ?? '-' }}</p>
            </div>
            <div>
                <h5 class="text-black text-xl font-medium mb-1">Tanggal</h5>
                <p class="text-slate-700 text-lg">{{ $violation->created_at ? $violation->created_at->format('Y-m-d') : '-' }}</p>
            </div>
            <div>
                <h5 class="text-black text-xl font-medium mb-1">Ketentuan</h5>
                <p class="text-slate-700 text-lg">{{ $violation->rule->name ?? '-' }}</p>
            </div>
            <div>
                <h5 class="text-black text-xl font-medium mb-1">Deskripsi</h5>
                <p class="text-slate-700 text-lg">
                    @if($violation->attendance)
                        Absen pada {{ $violation->attendance->date }} ({{ $violation->attendance->time_in ?? '-' }})
                    @else
                        Tidak ada absen (Bolos)
                    @endif
                </p>
            </div>
            <div>
                <h5 class="text-black text-xl font-medium mb-1">Hukuman</h5>
                <p class="text-slate-700 text-lg">{{ $violation->rule->points ?? '-' }} poin</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('btn-benar').onclick = function() {
    window.location.href = "{{ route('dashboard.pelanggaran') }}";
};

document.getElementById('btn-salah').onclick = function() {
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Yakin ingin menghapus pelanggaran ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("{{ route('dashboard.pelanggaran.delete', $violation->id) }}", {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Pelanggaran berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('dashboard.pelanggaran') }}";
                    });
                } else {
                    Swal.fire('Gagal', 'Gagal menghapus pelanggaran.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Terjadi kesalahan.', 'error'));
        }
    });
};
</script>
@endpush
