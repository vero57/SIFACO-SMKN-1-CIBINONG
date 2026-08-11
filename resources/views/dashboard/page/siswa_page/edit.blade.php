@extends('dashboard.layout.app', ['title' => isset($detail) ? 'Edit Detail Siswa' : 'Tambah Detail Siswa'])

@section('content')
<div class="content-section mx-4">
    <div class="bg-white backdrop-blur-sm rounded-xl p-6 px-10 border border-slate-300">
        <h3 class="text-3xl text-center font-semibold text-black mb-4">{{ isset($detail) ? 'Edit Detail Siswa' : 'Tambah Detail Siswa' }}</h3>
        <form action="{{ isset($detail) ? route('dashboard.siswa.detail.update', $user->id) : route('dashboard.siswa.detail.store', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-2 gap-6 pb-4">
                <div class="">
                    <label class="block text-black mb-1 text-sm">Nama Siswa</label>
                    <input type="text" value="{{ $user->name }}" disabled class="w-full bg-[#ecedf7] text-slate-700 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                </div>
                <div class="">
                    <label class="block text-black mb-1 text-sm">NIS</label>
                    <input type="text" name="nis" value="{{ old('nis', $detail->nis ?? '') }}" required class="w-full bg-[#ecedf7] text-slate-700 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                </div>
                <div class="">
                    <label class="block text-black mb-1 text-sm">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn', $detail->nisn ?? '') }}" class="w-full bg-[#ecedf7] text-slate-700 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                </div>
                <div class="">
                    <label class="block text-black mb-1 text-sm">Gender</label>
                    <select name="gender" required class="w-full bg-[#ecedf7] text-slate-700 border border-slate-700 rounded-lg px-3 py-3 text-sm">
                        <option value="" disabled {{ !old('gender', $detail->gender ?? '') ? 'selected' : '' }}>-- Pilih Gender --</option>
                        <option value="L" {{ old('gender', $detail->gender ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $detail->gender ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="">
                    <label class="block text-black mb-1 text-sm">Tempat Lahir</label>
                    <input type="text" name="birth_place" value="{{ old('birth_place', $detail->birth_place ?? '') }}" required class="w-full bg-[#ecedf7] text-slate-700 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                </div>
                <div class="">
                    <label class="block text-black mb-1 text-sm">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $detail->birth_date ?? '') }}" required class="w-full bg-[#ecedf7] text-slate-700 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                </div>
                <div class="">
                    <label class="block text-black mb-1 text-sm">Alamat</label>
                    <input type="text" name="address" value="{{ old('address', $detail->address ?? '') }}" required class="w-full bg-[#ecedf7] text-slate-700 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                </div>
                <div class="">
                    <label class="block text-black mb-1 text-sm">Foto</label>
                    <input type="file" name="photo" accept="image/*" class="w-full bg-[#ecedf7] text-slate-700 border border-slate-700 rounded-lg px-3 py-3 text-sm" />
                    @if(isset($detail) && $detail->photo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $detail->photo) }}" alt="Foto" class="w-16 h-16 object-cover rounded-lg" />
                        </div>
                    @endif
                    <p class="text-xs text-slate-700 mt-1">Max file 2mb. Format: jpg, png, jpeg</p>
                </div>
            </div>
            <div class="flex gap-6 mt-6 w-full">
                <a href="{{ route('dashboard.siswa') }}" class="w-1/2 bg-slate-600 hover:bg-slate-500 text-white px-4 py-2 rounded-lg text font-semibold text-center">Cancel</a>
                <button type="submit" class="w-1/2 bg-yellow-600 hover:bg-yellow-500 text-white px-4 py-2 rounded-lg text font-semibold">Simpan</button>
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
@endsection
