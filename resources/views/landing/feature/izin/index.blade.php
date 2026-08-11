@extends("landing.layout.app", ["title" => "SIFARECO"])

@push("style")
    <style>
        .izin-container {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .izin {
            width: 45%;
            background-color: #FFFFFFCC;
            border-radius: 1.5rem;
            padding: 2em;
            padding-top: 3rem;
            border: 2px solid #e5e7eb;
            backdrop-filter: blur(10px);
        }

        @media (max-width: 900px) {
            .izin-container {
                flex-direction: column;
                gap: 0rem;
                margin-bottom: 0rem;

            }

            .izin {
                width: 100%;
                background: none;
                border: none;
                padding: 0 1.5rem 2rem 1.5rem;
                margin-bottom: 0rem;
            }
        }

        .izin-form-input {
            font-size: 0.95rem;
            padding-top: 5px;
            padding-bottom: 5px;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .drop-zone {
            border: 2px dashed #64748b;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s;
            background: rgba(255, 255, 255, 0.02);
        }

        .drop-zone.dragover {
            border-color: #3b82f6;
            background: rgba(16, 185, 129, 0.1);
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
    <div class="">
        @include("landing.partials.header")

        <div id="customAlertModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-6">
            <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl shadow-black/20 text-slate-900">
                <div class="flex flex-col items-center gap-4 text-center">
                    <div id="customAlertIcon" class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <span class="material-symbols-outlined text-4xl">check_circle</span>
                    </div>
                    <div>
                        <h2 id="customAlertTitle" class="text-xl font-semibold"></h2>
                        <p id="customAlertMessage" class="mt-2 text-slate-600"></p>
                    </div>
                    <button id="customAlertClose" type="button" class="mt-4 inline-flex items-center justify-center rounded-full bg-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-primary-container">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    openCustomAlert('success', 'Berhasil!', @json(session('success')), 3000);
                });
            </script>
        @endif
        @if($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    openCustomAlert('error', 'Error!', @json(implode('<br/>', $errors->all())), 0);
                });
            </script>
        @endif
        <div class="text-center mb-1 lg:mb-16 max-sm:mt-6 max-sm:py-4 max-sm:px-4 max-sm:mx-4 pb-1 max-sm:bg-white rounded-xl max-sm:border max-sm:border-[#e5e7eb] max-sm:shadow-lg">
            <h1 class="text-xl lg:text-[32px] font-semibold text-[#0b1c30] mb-2 lg:pt-16 max-sm:text-start">Pengajuan Izin Siswa</h1>
            <p class="max-sm:text-sm text-gray-600 max-sm:text-start">Silakan lengkapi formulir di bawah untuk mengajukan izin ketidakhadiran.</p>
        </div>
        <form action="{{ route('feature.izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            <div class="izin-container lg:flex-col lg:items-center">
                    @csrf
                    <!-- Kontainer Atas -->
                    <div class="izin flex items-center justify-center">
                        <div class="w-full max-sm:max-w-md max-w-xl space-y-4 md:space-y-6">

                            <div class="flex flex-col lg:flex-row gap-4">
                                <div class="flex flex-col lg:flex-row lg:gap-2 w-full">
                                    <div class="lg:w-full">
                                        <label for="student_id" class="block text-[#0b1c30] mb-1">Nama Siswa</label>
                                        <input type="hidden" name="student_id" value="{{ auth()->user()->id }}">
                                        <input type="text" id="student_id_display" value="{{ auth()->user()->name }}"
                                            readonly
                                            class="izin-form-input w-full rounded-lg bg-[#eff4ff] max-sm:placeholder-shown:bg-white text-[#0b1c30] border-2 border-[#e5e7eb] focus:outline-none focus:ring-2 focus:ring-blue-600 cursor-not-allowed">
                                    </div>
                                </div>

                                <div class="flex flex-col lg:flex-row lg:gap-2 w-full">
                                    <div class="lg:w-full">
                                        <label for="parent_name" class="block text-[#0b1c30] mb-1">Nama Orang Tua /
                                            Wali</label>
                                        <input type="text" id="parent_name" name="parent_name" required
                                            class="izin-form-input w-full rounded-lg bg-[#eff4ff] max-sm:placeholder-shown:bg-white text-[#0b1c30] border-2 border-[#e5e7eb] focus:outline-none focus:ring-2 focus:ring-blue-600"
                                            placeholder="Masukkan nama wali">
                                    </div>
                                </div>
                            </div>


                            <div>
                                <label for="type" class="block text-[#0b1c30] mb-1">Tipe Izin</label>
                                <select id="type" name="type" required
                                    class="izin-form-input w-full rounded-lg bg-[#eff4ff] max-sm:placeholder-shown:bg-white text-[#0b1c30] py-2.5 text-base border-2 border-[#e5e7eb] focus:outline-none focus:ring-2 focus:ring-blue-600">
                                    <option value="">Pilih Alasan Izin</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="izin">Izin</option>
                                    <option value="dispen">Dispen</option>
                                </select>
                            </div>
                            <div>
                                <label for="description" class="block text-[#0b1c30] mb-1">Deskripsi / Alasan Detail</label>
                                <textarea id="description" name="description" rows="3" required
                                    class="w-full rounded-lg bg-[#eff4ff] max-sm:placeholder-shown:bg-white text-[#0b1c30] text-base border-2 border-[#e5e7eb] px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600"
                                    placeholder="Berikan Penjelasan singkat mengenai izin"></textarea>
                            </div>
                            <div class="flex flex-col items-center justify-center md:pb-4">
                                <div class="flex flex-col items-start w-full">
                                    <label for="drop-zone" class="block text-[#0b1c30] mb-1">Upload Surat Izin</label>
                                </div>
                                <div id="drop-zone" class="drop-zone w-full">
                                    <svg class="w-20 h-20 text-slate-300 mb-6 mx-auto" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <div class="text-center px-4">
                                        <h2 class="text-lg font-semibold text-[#0b1c30] mb-2">Upload File</h2>
                                        <p class="text-slate-400" id="drop-text">Klik atau seret file ke sini</p>
                                        <p class="text-xs text-slate-400 mt-1">Max file 10mb. Format: jpg, png, jpeg, pdf
                                        </p>
                                    </div>
                                </div>
                                <input type="file" id="file-input" name="file[]" class="file-input"
                                    accept="image/*,application/pdf" required multiple>
                            </div>
                            <button type="button" id="btn-berikutnya"
                                class="w-full max-sm:mt-4 md:mt-8 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">Berikutnya</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>

        @include("landing.partials.nav")
    </section>

@push("script")
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const dropText = document.getElementById('drop-text');
    const btnBerikutnya = document.getElementById('btn-berikutnya');
    const form = document.querySelector('form');
    const customAlertModal = document.getElementById('customAlertModal');
    const customAlertIcon = document.getElementById('customAlertIcon');
    const customAlertTitle = document.getElementById('customAlertTitle');
    const customAlertMessage = document.getElementById('customAlertMessage');
    const customAlertClose = document.getElementById('customAlertClose');
    let customAlertTimeout = null;

    function openCustomAlert(type, title, message, duration = 0) {
        if (!customAlertModal) return;

        if (customAlertTimeout) {
            clearTimeout(customAlertTimeout);
            customAlertTimeout = null;
        }

        customAlertTitle.textContent = title;
        customAlertMessage.innerHTML = message;

        if (type === 'success') {
            customAlertIcon.className = 'flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600';
            customAlertIcon.innerHTML = '<span class="material-symbols-outlined text-4xl">check_circle</span>';
        } else if (type === 'error') {
            customAlertIcon.className = 'flex h-16 w-16 items-center justify-center rounded-full bg-rose-100 text-rose-600';
            customAlertIcon.innerHTML = '<span class="material-symbols-outlined text-4xl">error</span>';
        } else {
            customAlertIcon.className = 'flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-700';
            customAlertIcon.innerHTML = '<span class="material-symbols-outlined text-4xl">info</span>';
        }

        customAlertModal.classList.remove('hidden');
        customAlertModal.classList.add('flex');

        if (duration > 0) {
            customAlertTimeout = setTimeout(closeCustomAlert, duration);
        }
    }

    function closeCustomAlert() {
        if (!customAlertModal) return;
        customAlertModal.classList.add('hidden');
        customAlertModal.classList.remove('flex');
        if (customAlertTimeout) {
            clearTimeout(customAlertTimeout);
            customAlertTimeout = null;
        }
    }

    if (customAlertClose) {
        customAlertClose.addEventListener('click', closeCustomAlert);
    }

                // Klik untuk pilih file
                dropZone.addEventListener('click', () => fileInput.click());

                // Drag over
                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropZone.classList.add('dragover');
                });

                // Drag leave
                dropZone.addEventListener('dragleave', () => {
                    dropZone.classList.remove('dragover');
                });

                // Drop
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

                // File input change
                fileInput.addEventListener('change', () => {
                    if (fileInput.files.length > 0) {
                        dropZone.classList.add('has-file');
                        dropText.textContent = `${fileInput.files.length} file(s) dipilih`;
                    } else {
                        dropZone.classList.remove('has-file');
                        dropText.textContent = 'Seret dan jatuhkan file di sini atau klik untuk memilih';
                    }
                });

    // Tombol Berikutnya: simpan data ke sessionStorage dan redirect
    btnBerikutnya.addEventListener('click', function(e) {
        e.preventDefault();
        // Validasi manual
        const parentName = document.getElementById('parent_name').value.trim();
        const type = document.getElementById('type').value;
        const description = document.getElementById('description').value.trim();
        if (!parentName || !type || !description || fileInput.files.length === 0) {
            openCustomAlert('error', 'Lengkapi Data!', 'Semua field dan file wajib diisi.', 0);
            return;
        }
        // Simpan data ke sessionStorage
        const izinData = {
            student_id: document.querySelector('input[name="student_id"]').value,
            parent_name: parentName,
            type: type,
            description: description
        };
        sessionStorage.setItem('izinData', JSON.stringify(izinData));
        // Simpan file ke sessionStorage (support multiple file)
        const filesArr = [];
        const fileNames = [];
        let filesLoaded = 0;
        for (let i = 0; i < fileInput.files.length; i++) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                filesArr[i] = evt.target.result;
                fileNames[i] = fileInput.files[i].name;
                filesLoaded++;
                if (filesLoaded === fileInput.files.length) {
                    sessionStorage.setItem('izinFiles', JSON.stringify(filesArr));
                    sessionStorage.setItem('izinFileNames', JSON.stringify(fileNames));
                    window.location.href = '/izin/face';
                }
            };
            reader.readAsDataURL(fileInput.files[i]);
        }
    });
});
</script>
@endpush
@endsection
