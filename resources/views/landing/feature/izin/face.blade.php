@extends("landing.layout.app", ["title" => "Verifikasi Wajah Izin"])

@push("style")
<style>
    .scanner-line {
        height: 2px;
        background: linear-gradient(90deg, transparent, #004ac6, transparent);
        box-shadow: 0 0 15px #004ac6;
        animation: scan 3s infinite linear;
    }

    @keyframes scan {
        0% {
            top: 0%;
        }

        50% {
            top: 100%;
        }

        100% {
            top: 0%;
        }
    }

    .pulse-border {
        animation: pulse-border 2s infinite ease-in-out;
    }

    @keyframes pulse-border {
        0% {
            border-color: rgba(0, 74, 198, 0.4);
        }

        50% {
            border-color: rgba(0, 74, 198, 1);
        }

        100% {
            border-color: rgba(0, 74, 198, 0.4);
        }
    }

    .glass-overlay {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .loader {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #004ac6;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .modal-error-title {
        color: #dc2626;
    }

    .modal-error-icon {
        color: #dc2626;
        font-size: 48px;
    }

    .modal-close-btn {
        margin-top: 1rem;
        padding: 0.5rem 1.5rem;
        background: #004ac6;
        color: white;
        border-radius: 0.5rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .modal-close-btn:hover {
        background: #003a9e;
    }
</style>
@endpush

@section("content")
<section class="min-h-screen text-on-surface bg-background">
    <main class="flex-grow w-full max-w-7xl mx-auto px-md md:px-margin-desktop py-md">
        <div class="text-center mb-10">
            <h1 class="text-2xl font-bold text-on-surface mb-2">Absen Wajah</h1>
            <p class="text-on-surface-variant">Silakan lakukan absensi kehadiran dengan verifikasi wajah di bawah ini.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
            <!-- Left Side -->
            <aside class="lg:col-span-3 flex flex-col gap-md">
                <!-- Progress Stepper -->
                <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant">
                    <h3 class="font-label-md text-label-md text-outline uppercase tracking-wider mb-md">Langkah Absensi</h3>
                    <div class="flex flex-col gap-lg">
                        <div class="flex items-center gap-sm">
                            <div class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold">1</div>
                            <div>
                                <p class="font-label-md text-label-md text-primary">Scan</p>
                                <p class="text-xs text-on-surface-variant">Posisikan wajah Anda</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-sm">
                            <div id="step-verify-badge" class="w-8 h-8 rounded-full bg-surface-container text-on-surface-variant flex items-center justify-center font-bold">2</div>
                            <div>
                                <p id="step-verify-title" class="font-label-md text-label-md text-on-surface-variant">Verify</p>
                                <p class="text-xs text-on-surface-variant opacity-60">Memproses data biometrik</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-sm">
                            <div id="step-done-badge" class="w-8 h-8 rounded-full bg-surface-container text-on-surface-variant flex items-center justify-center font-bold">3</div>
                            <div>
                                <p id="step-done-title" class="font-label-md text-label-md text-on-surface-variant">Done</p>
                                <p class="text-xs text-on-surface-variant opacity-60">Selesai terkirim</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Target Expression Card -->
                <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant flex flex-col items-center justify-center">
                    <h3 class="font-label-md text-label-md text-outline uppercase tracking-wider mb-sm w-full text-left font-semibold">Tirukan Ekspresi</h3>
                    <div class="w-32 aspect-square bg-surface-container-low rounded-xl flex items-center justify-center mb-sm overflow-hidden expression-box"></div>
                    <div class="text-center">
                        <h4 class="font-headline-md text-headline-md font-bold text-primary expression-label">-</h4>
                        <p class="text-xs text-on-surface-variant expression-desc">Tirukan ekspresi wajah di atas</p>
                    </div>
                </div>

                <!-- Guidance Card -->
                <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant">
                    <div class="flex items-center gap-xs text-primary mb-base">
                        <span class="material-symbols-outlined text-body-lg">info</span>
                        <h4 class="font-label-md text-label-md">Instruksi</h4>
                    </div>
                    <ul class="space-y-sm text-body-sm text-on-surface-variant">
                        <li class="flex gap-xs"><span class="text-primary">•</span> Lepaskan kacamata hitam/masker</li>
                        <li class="flex gap-xs"><span class="text-primary">•</span> Pastikan pencahayaan ruangan cukup</li>
                        <li class="flex gap-xs"><span class="text-primary">•</span> Hadapkan wajah tegak ke depan</li>
                        <li class="flex gap-xs"><span class="text-primary">•</span> Tetap tenang saat pemindaian</li>
                    </ul>
                </div>
            </aside>

            <!-- Center: Camera -->
            <section class="lg:col-span-6 flex flex-col gap-md">
                <div class="relative aspect-[4/3] w-full bg-inverse-surface rounded-xl overflow-hidden shadow-xl border-4 border-surface-container-lowest group flex items-center justify-center">
                    <div id="cameraLoading" class="flex flex-col items-center justify-center w-full h-full text-white/70">
                        <div class="loader mb-4"></div>
                        <div class="font-label-md text-label-md">Memulai kamera...</div>
                    </div>

                    <div id="cameraContainer" style="display:none; width:100%; height:100%;" class="relative w-full h-full flex items-center justify-center">
                        <video id="video" autoplay muted class="w-full h-full object-cover rounded-lg bg-neutral-900"></video>
                        <canvas id="canvas" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-48 h-48 md:w-64 md:h-64 border-2 border-primary/40 rounded-[32px] relative pulse-border">
                                <div class="absolute -top-1 -left-1 w-6 h-6 border-t-4 border-l-4 border-primary rounded-tl-xl"></div>
                                <div class="absolute -top-1 -right-1 w-6 h-6 border-t-4 border-r-4 border-primary rounded-tr-xl"></div>
                                <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-4 border-l-4 border-primary rounded-bl-xl"></div>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-4 border-r-4 border-primary rounded-br-xl"></div>
                                <div class="absolute inset-0 w-full overflow-hidden rounded-[30px]">
                                    <div class="scanner-line relative w-full"></div>
                                </div>
                            </div>
                        </div>

                        <div class="absolute bottom-md left-1/2 -translate-x-1/2 glass-overlay px-lg py-sm rounded-full shadow-lg z-20">
                            <p class="font-headline-md text-headline-md text-primary text-center whitespace-nowrap expression-overlay-label">Silakan Ikuti Instruksi</p>
                        </div>
                    </div>

                    <div class="absolute top-md right-md flex flex-col gap-xs items-end z-10">
                        <div class="bg-black/60 text-white px-base py-xs rounded-lg text-xs backdrop-blur-md flex items-center gap-xs">
                            <span class="w-2 h-2 rounded-full bg-secondary-fixed animate-pulse"></span>
                            LIVE FEED
                        </div>
                    </div>
                </div>
            </section>

            <!-- Right Side -->
            <aside class="lg:col-span-3 flex flex-col gap-md">
                <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant h-full">
                    <h3 class="font-label-md text-label-md text-outline uppercase tracking-wider mb-md">Status Kehadiran</h3>
                    <div class="space-y-md">
                        <div class="flex flex-col items-center p-md bg-surface-container-low rounded-xl border border-primary/10">
                            <div class="w-20 h-20 rounded-full bg-outline-variant mb-sm overflow-hidden border-2 border-surface-container-lowest flex items-center justify-center">
                                <span class="material-symbols-outlined text-outline text-4xl">account_circle</span>
                            </div>
                            <p class="font-label-md text-label-md text-primary status-text">Mendeteksi Wajah...</p>
                        </div>
                        <div class="pt-base border-t border-outline-variant">
                            <h4 class="font-label-sm text-label-sm text-outline-variant mb-sm">Log Terdeteksi</h4>
                            <div class="ekspresi-terdeteksi text-body-sm text-on-surface-variant font-medium p-sm bg-surface-container-low rounded-lg">Menunggu deteksi wajah...</div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </main>
</section>

<!-- Processing Modal -->
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[100] hidden items-center justify-center" id="processingModal">
        <div class="bg-surface rounded-2xl p-xl max-w-sm w-full mx-md text-center shadow-2xl flex flex-col items-center gap-md">
            <div class="relative flex items-center justify-center">
                <div class="w-16 h-16 border-4 border-surface-container-high border-t-primary rounded-full animate-spin"></div>
                <span class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-primary text-3xl">face</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <h2 class="font-headline-md text-headline-md text-on-surface modal-title">Memverifikasi Wajah</h2>
                <p class="text-on-surface-variant mt-xs modal-description">Mohon tunggu sebentar, sistem sedang memvalidasi data Anda.</p>
            </div>
        </div>
    </div>

    <!-- Custom Alert Modal -->
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[101] hidden items-center justify-center" id="customAlertModal">
        <div class="bg-surface rounded-3xl p-8 max-w-md w-full mx-4 text-center shadow-2xl shadow-black/20">
            <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-500" id="customAlertIcon">
                <span class="material-symbols-outlined text-4xl">check_circle</span>
            </div>
            <h2 class="font-headline-md text-headline-md text-on-surface mb-2" id="customAlertTitle">Judul</h2>
            <p class="text-body-md text-on-surface-variant mb-6" id="customAlertMessage">Pesan</p>
            <button id="customAlertButton" class="inline-flex items-center justify-center rounded-full bg-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-primary-container">
                Tutup
            </button>
        </div>
    </div>
@endsection

@push('script')
<script src="/faceapi/face-api.min.js"></script>
<script src="/faceapi/scripts.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");
    const cameraLoading = document.getElementById("cameraLoading");
    const cameraContainer = document.getElementById("cameraContainer");
    const customAlertModal = document.getElementById('customAlertModal');
    const customAlertIcon = document.getElementById('customAlertIcon');
    const customAlertTitle = document.getElementById('customAlertTitle');
    const customAlertMessage = document.getElementById('customAlertMessage');
    const customAlertButton = document.getElementById('customAlertButton');
    let customAlertTimeout = null;
    let customAlertCallback = null;
    let izinData = null, izinFiles = null, izinFileNames = null;
    let submitInProgress = false;

    function openCustomAlert(type, title, message, duration = 0, callback = null) {
        if (!customAlertModal || !customAlertTitle || !customAlertMessage || !customAlertIcon) return;

        if (customAlertTimeout) {
            clearTimeout(customAlertTimeout);
            customAlertTimeout = null;
        }

        customAlertTitle.textContent = title;
        customAlertMessage.innerHTML = message;
        customAlertCallback = typeof callback === 'function' ? callback : null;

        if (type === 'success') {
            customAlertIcon.innerHTML = '<span class="material-symbols-outlined text-4xl text-emerald-500">check_circle</span>';
        } else if (type === 'error') {
            customAlertIcon.innerHTML = '<span class="material-symbols-outlined text-4xl text-rose-500">error</span>';
        } else {
            customAlertIcon.innerHTML = '<span class="material-symbols-outlined text-4xl text-slate-500">info</span>';
        }

        customAlertModal.classList.remove('hidden');
        customAlertModal.classList.add('flex');

        if (duration > 0) {
            customAlertTimeout = setTimeout(() => {
                customAlertTimeout = null;
                closeCustomAlert();
            }, duration);
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
        if (customAlertCallback) {
            const callback = customAlertCallback;
            customAlertCallback = null;
            callback();
        }
    }

    if (customAlertButton) {
        customAlertButton.addEventListener('click', closeCustomAlert);
    }

    // Ambil data izin dari sessionStorage
    try {
        izinData = JSON.parse(sessionStorage.getItem('izinData'));
        izinFiles = JSON.parse(sessionStorage.getItem('izinFiles'));
        izinFileNames = JSON.parse(sessionStorage.getItem('izinFileNames'));
    } catch (e) {}

    if (!izinData || !izinFiles || !izinFileNames) {
        openCustomAlert('error', 'Data tidak lengkap', 'Silakan isi form izin terlebih dahulu.', 0, function() {
            window.location.href = '/izin';
        });
        return;
    }

    // Otomatis jalankan kamera
    navigator.mediaDevices.getUserMedia({ video: {} })
        .then(stream => {
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                cameraLoading.style.display = "none";
                cameraContainer.style.display = "flex";
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
            };
        })
        .catch(err => {
            cameraLoading.innerHTML = "<div class='text-red-500'>Gagal mengakses kamera: " + err.message + "</div>";
        });

    // Patch scripts.js: submit jika wajah dikenali
    window.absenEkspresiCheck = function(ekspresiTerdeteksi, confidence, faceLabel) {
        if (submitInProgress) return;
        if (faceLabel === "unknown" || !faceLabel) {
            const ekspresiDetected = document.querySelector('.ekspresi-terdeteksi');
            if (ekspresiDetected) {
                ekspresiDetected.innerHTML = "<span style='color:#f87171;'>Wajah tidak dikenali sebagai user. Tidak bisa mengirim izin.</span>";
            }
            return;
        }
        submitInProgress = true;
        const ekspresiDetected = document.querySelector('.ekspresi-terdeteksi');
        ekspresiDetected.innerHTML = "Mengambil lokasi...";

        // Ambil lokasi sebelum submit
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                ekspresiDetected.innerHTML = "Mengirim pengajuan izin...";
                submitIzin(pos.coords.latitude, pos.coords.longitude);
            }, function(err) {
                ekspresiDetected.innerHTML = "<span style='color:#f87171;'>Gagal mengambil lokasi: " + err.message + "</span>";
                submitInProgress = false;
            }, { enableHighAccuracy: true });
        } else {
            ekspresiDetected.innerHTML = "<span style='color:#f87171;'>Browser tidak mendukung geolokasi.</span>";
            submitInProgress = false;
        }
    };

    function submitIzin(lat, lng) {
        // Ambil foto dari video (canvas)
        const canvasTemp = document.createElement("canvas");
        canvasTemp.width = video.videoWidth;
        canvasTemp.height = video.videoHeight;
        const ctx = canvasTemp.getContext("2d");
        ctx.drawImage(video, 0, 0, canvasTemp.width, canvasTemp.height);
        const photoData = canvasTemp.toDataURL("image/jpeg", 0.92);

        // Kirim data ke backend via FormData
        const formData = new FormData();
        formData.append('student_id', izinData.student_id);
        formData.append('parent_name', izinData.parent_name);
        formData.append('type', izinData.type);
        formData.append('description', izinData.description);
        formData.append('photo', photoData);
        formData.append('location_lat', lat);
        formData.append('location_lng', lng);
        for (let i = 0; i < izinFiles.length; i++) {
            // Convert base64 to Blob
            const arr = izinFiles[i].split(','), mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
            for (let j = 0; j < n; j++) u8arr[j] = bstr.charCodeAt(j);
            formData.append('file[]', new Blob([u8arr], {type: mime}), izinFileNames[i]);
        }

        fetch("{{ route('feature.izin.store') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: formData
        })
        .then(async res => {
            // Jika redirect, berarti berhasil
            if (res.redirected) {
                sessionStorage.removeItem('izinData');
                sessionStorage.removeItem('izinFiles');
                sessionStorage.removeItem('izinFileNames');
                openCustomAlert('success', 'Berhasil!', 'Izin berhasil diajukan.', 2500, function() {
                    window.location.href = res.url;
                });
                return;
            }
            // Jika response json (gagal)
            let data = {};
            try { data = await res.json(); } catch {}
            throw new Error(data.message || 'Gagal mengirim izin');
        })
        .catch((err)=>{
            const ekspresiDetected = document.querySelector('.ekspresi-terdeteksi');
            ekspresiDetected.innerHTML = "<span style='color:#f87171;'>Gagal mengirim izin: "+(err.message||'')+"</span>";
            openCustomAlert('error', 'Gagal!', err.message || 'Gagal mengirim izin.', 0);
            submitInProgress = false;
        })
        .finally(() => {
            sessionStorage.removeItem('izinData');
            sessionStorage.removeItem('izinFiles');
            sessionStorage.removeItem('izinFileNames');
        });
    }
});
</script>
@endpush
