@extends("landing.layout.app", ["title" => "Pengisian Jurnal"])

@push("style")
<style>
    .jurnal-form-box {
        background: rgba(255,255,255,0.05);
        border-radius: 1.5rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        backdrop-filter: blur(10px);
    }
</style>
@endpush

@section("content")
<section class="min-h-screen text-slate-200">
    <div class="container mx-auto px-6 py-8">
        @include("landing.partials.header")
        @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session("success") }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            </script>
        @endif
        @if($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: '<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                });
            </script>
        @endif
        <div class="text-center mb-1">
            <h1 class="text-2xl font-bold text-slate-100 mb-1">Pengisian Jurnal</h1>
            <p class="text-slate-400">Pengisian Jurnal, diisi tiap mapel selesai</p>
        </div>
        <div class="jurnal-container flex mt-6 lg:mx-5">
            <div class="jurnal-form-box w-full bg-white/5 rounded-2xl p-6 lg:p-8 shadow-lg backdrop-blur-md">
                <form action="{{ route('feature.jurnal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div>
                        <label for="student_id_display" class="block text-slate-300 mb-1">Pengisi Jurnal</label>
                        <input type="hidden" name="student_id" value="{{ auth()->user()->id }}">
                        <input type="text" id="student_id_display" value="{{ auth()->user()->name }}" readonly class="w-full rounded-lg bg-slate-800 text-slate-100 border border-slate-700 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label for="subject_id" class="block text-slate-300 mb-1">Mata Pelajaran</label>
                        <select id="subject_id" name="subject_id" required class="w-full rounded-lg bg-slate-800 text-slate-100 border border-slate-700 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400">
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="description" class="block text-slate-300 mb-1">Deskripsi</label>
                        <textarea id="description" name="description" rows="3" required class="w-full rounded-lg bg-slate-800 text-slate-100 border border-slate-700 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400"></textarea>
                    </div>
                    <div>
                        <label for="foto" class="block text-slate-300 mb-1">Upload Foto</label>
                        <input type="file" id="foto" name="foto" accept="image/*" required class="w-full rounded-lg bg-slate-800 text-slate-100 border border-slate-700 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400">
                        <p class="text-xs text-slate-400 mt-1">Max file 5mb. Format: jpg, png, jpeg</p>
                    </div>
                    <button type="submit" class="w-full bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 rounded-lg transition">Kirim Jurnal</button>
                </form>
            </div>
        </div>
    </div>

    @include("landing.partials.nav")
</section>
@endsection
