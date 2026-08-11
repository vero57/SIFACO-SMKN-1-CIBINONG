@extends("landing.layout.app", ["title" => "Pengisian Jurnal"])

@push("style")
<style>
    .jurnal-container{
        width: 60%;
    }
    @media (max-width: 640px) {
        .jurnal-container{
            width: 90%;
        }
    }
    .jurnal-form-box {
        background: rgba(255,255,255,0.05);
        border-radius: 1.5rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        backdrop-filter: blur(10px);
    }
    .drop-zone {
        border: 2px dashed #64748b;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.3s;
        background: #f1f5f9;
    }
    .drop-zone.dragover {
        border-color: #3b82f6;
        background: #e6ebf0;
    }
    .drop-zone.has-file {
        border-color: #3b82f6;
        background: rgba(59, 130, 246, 0.05);
    }
    .file-input {
        display: none;
    }
</style>
@endpush

@section("content")
<section class="min-h-screen text-slate-200">
    <div class="pb-8">
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
        <div class="flex flex-col items-center">

            <div class="jurnal-container flex flex-col lg:flex-row lg:gap-5 mt-7 lg:mt-14 lg:mx-5">
                <div class="w-full lg:w-2/5 flex flex-col justify-center text-start mb-1">
                    <h1 class="text-2xl lg:text-3xl font-semibold text-[#0b1c30] mb-1 lg:mb-3">Jurnal Harian Siswa</h1>
                    <p class="text-[#0b1c30] mb-6">Dokumentasikan setiap langkah perjalanan belajarmu hari ini. Catat aktivitas, pencapaian, dan momen berharga di sekolah.</p>
                    <div class="bg-surface-container-low lg:mb-6 p-md rounded-xl space-y-sm border border-outline-variant/30">
                        <div class="flex items-center gap-sm">
                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: &quot;FILL&quot; 1">history_edu</span>
                            <span class="font-label-md text-label-md text-on-surface">Panduan Pengisian</span>
                        </div>
                        <ul class="space-y-xs text-body-sm text-on-surface-variant list-disc list-inside" >
                            <li>Pilih mata pelajaran yang dipelajari</li>
                            <li>Tulis deskripsi kegiatan secara ringkas</li>
                            <li>Unggah foto kegiatan sebagai bukti</li>
                        </ul>
                    </div>
                    <div class="hidden lg:block relative rounded-2xl overflow-hidden aspect-video shadow-lg">
                        <img class="w-full h-full object-cover" data-alt="" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBTVl1oAxgptpEsvwSsRdWriTzd3mymcy-wikbDzvn2OdAGiPWqAxzHRPPHOHF1FuiH8dyG0J7h5MHvXFZcXbBg3dEl9kMfyr2rdclqO4AU-FhrguARn8fO3YFFsbPc8igQBMngQ_TG19QjHNwCquGBv7Dk_s0XcUV1qZYuClf9GMhYeoPh3oVUBx587R5MsiNBtzvCNE0ujZvamJB8BvcF7VGwU3EKJ67Qq-uxLcLvsqV7lpPnWfl2GwRXSFW9_o_DID67__bDOUk" />
                    </div>
                </div>
                <div class="lg:jurnal-form-box w-full lg:w-3/5 lg:bg-white lg:border-2 lg:border-slate-300 rounded-3xl lg:p-6 lg:p-8 lg:px-14">
                    <form action="{{ route('feature.jurnal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 lg:space-y-5">
                        @csrf
                        <div>
                            <label for="student_id_display" class="block text-[#0b1c30] mb-1">Pengisi Jurnal</label>
                            <input type="hidden" name="student_id" value="{{ auth()->user()->id }}">
                            <input type="text" id="student_id_display" value="{{ auth()->user()->name }}" readonly class="w-full rounded-lg lg:bg-[#f1f5f9] text-[#0b1c30] border border-slate-300 lg:border-[#f1f5f9] px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label for="subject_id" class="block text-[#0b1c30] mb-1">Mata Pelajaran</label>
                            <select id="subject_id" name="subject_id" required class="w-full rounded-lg lg:bg-[#f1f5f9] text-[#0b1c30] border border-slate-300 lg:border-[#f1f5f9] px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="description" class="block text-[#0b1c30] mb-1">Deskripsi</label>
                            <textarea id="description" name="description" rows="3" required class="w-full rounded-lg lg:bg-[#f1f5f9] text-[#0b1c30] border border-slate-300 lg:border-[#f1f5f9] px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label for="foto" class="block text-[#0b1c30] mb-1">Foto Kegiatan / Jurnal</label>
                            <div id="drop-zone" class="drop-zone w-full">
                                <svg class="w-20 h-20 text-slate-300 mb-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div class="text-center px-4">
                                    <h2 class="text-lg font-semibold text-[#0b1c30] mb-2">Upload Foto</h2>
                                    <p class="text-slate-400" id="drop-text">Klik atau seret file ke sini</p>
                                    <p class="text-xs text-slate-400 mt-1">Max file 5mb. Format: jpg, png, jpeg</p>
                                </div>
                            </div>
                            <input type="file" id="file-input" name="foto" accept="image/*" required class="file-input">
                        </div>
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-500 text-white font-semibold py-2 rounded-lg transition">Simpan Jurnal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include("landing.partials.nav")
</section>

@push("script")
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const dropText = document.getElementById('drop-text');

        if (!dropZone || !fileInput || !dropText) return;

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                dropZone.classList.add('has-file');
                dropText.textContent = `${files.length} file(s) dipilih`;
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                dropZone.classList.add('has-file');
                dropText.textContent = `${fileInput.files.length} file(s) dipilih`;
            } else {
                dropZone.classList.remove('has-file');
                dropText.textContent = 'Klik atau seret file ke sini';
            }
        });
    });
</script>
@endpush
@endsection
