@extends("landing.layout.app", ["title" => "ManaMukanya"])

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

        /* Loader animation */
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
    </style>
@endpush

@section("content")
    <section class="min-h-screen text-on-surface bg-background">
        @include("landing.partials.header")

        <!-- Main Content Canvas -->
        <main class="flex-grow w-full max-w-7xl mx-auto px-md md:px-margin-desktop py-md">
            <div class="text-center mb-10">
                <h1 class="text-2xl font-bold text-on-surface mb-2">Absen Wajah</h1>
                <p class="text-on-surface-variant">Silakan lakukan absensi kehadiran dengan verifikasi wajah di bawah ini.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
                <!-- Left Side: Progress & Instructions (Desktop) -->
                <aside class="lg:col-span-3 flex flex-col gap-md">
                    <!-- Progress Stepper -->
                    <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant">
                        <h3 class="font-label-md text-label-md text-outline uppercase tracking-wider mb-md">Langkah Absensi
                        </h3>
                        <div class="flex flex-col gap-lg">
                            <div class="flex items-center gap-sm">
                                <div
                                    class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold">
                                    1</div>
                                <div>
                                    <p class="font-label-md text-label-md text-primary">Scan</p>
                                    <p class="text-xs text-on-surface-variant">Posisikan wajah Anda</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-sm">
                                <div id="step-verify-badge"
                                    class="w-8 h-8 rounded-full bg-surface-container text-on-surface-variant flex items-center justify-center font-bold">
                                    2</div>
                                <div>
                                    <p id="step-verify-title" class="font-label-md text-label-md text-on-surface-variant">
                                        Verify</p>
                                    <p class="text-xs text-on-surface-variant opacity-60">Memproses data biometrik</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-sm">
                                <div id="step-done-badge"
                                    class="w-8 h-8 rounded-full bg-surface-container text-on-surface-variant flex items-center justify-center font-bold">
                                    3</div>
                                <div>
                                    <p id="step-done-title" class="font-label-md text-label-md text-on-surface-variant">Done
                                    </p>
                                    <p class="text-xs text-on-surface-variant opacity-60">Selesai terkirim</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Target Expression Card -->
                    <div
                        class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant flex flex-col items-center justify-center">
                        <h3
                            class="font-label-md text-label-md text-outline uppercase tracking-wider mb-sm w-full text-left font-semibold">
                            Tirukan Ekspresi</h3>
                        <div
                            class="w-32 aspect-square bg-surface-container-low rounded-xl flex items-center justify-center mb-sm overflow-hidden expression-box">
                            <!-- Filled by JS -->
                        </div>
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
                            <li class="flex gap-xs"><span class="text-primary">•</span> Pastikan pencahayaan ruangan cukup
                            </li>
                            <li class="flex gap-xs"><span class="text-primary">•</span> Hadapkan wajah tegak ke depan</li>
                            <li class="flex gap-xs"><span class="text-primary">•</span> Tetap tenang saat pemindaian</li>
                        </ul>
                    </div>
                </aside>

                <!-- Center: Camera Viewfinder -->
                <section class="lg:col-span-6 flex flex-col gap-md">
                    <div
                        class="relative aspect-[4/3] w-full bg-inverse-surface rounded-xl overflow-hidden shadow-xl border-4 border-surface-container-lowest group flex items-center justify-center">
                        <!-- Loading camera stream -->
                        <div id="cameraLoading"
                            class="flex flex-col items-center justify-center w-full h-full text-white/70">
                            <div class="loader mb-4"></div>
                            <div class="font-label-md text-label-md">Memulai kamera...</div>
                        </div>

                        <!-- Camera & Canvas Container -->
                        <div id="cameraContainer" style="display:none; width:100%; height:100%;"
                            class="relative w-full h-full flex items-center justify-center">
                            <video id="video" autoplay muted
                                class="w-full h-full object-cover rounded-lg bg-neutral-900"></video>
                            <canvas id="canvas" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                            <!-- Scanning Frame -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div
                                    class="w-48 h-48 md:w-64 md:h-64 border-2 border-primary/40 rounded-[32px] relative pulse-border">
                                    <!-- Corner Accents -->
                                    <div
                                        class="absolute -top-1 -left-1 w-6 h-6 border-t-4 border-l-4 border-primary rounded-tl-xl">
                                    </div>
                                    <div
                                        class="absolute -top-1 -right-1 w-6 h-6 border-t-4 border-r-4 border-primary rounded-tr-xl">
                                    </div>
                                    <div
                                        class="absolute -bottom-1 -left-1 w-6 h-6 border-b-4 border-l-4 border-primary rounded-bl-xl">
                                    </div>
                                    <div
                                        class="absolute -bottom-1 -right-1 w-6 h-6 border-b-4 border-r-4 border-primary rounded-br-xl">
                                    </div>
                                    <!-- Scanning Line -->
                                    <div class="absolute inset-0 w-full overflow-hidden rounded-[30px]">
                                        <div class="scanner-line relative w-full"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Instruction Overlay -->
                            <div
                                class="absolute bottom-md left-1/2 -translate-x-1/2 glass-overlay px-lg py-sm rounded-full shadow-lg z-20">
                                <p
                                    class="font-headline-md text-headline-md text-primary text-center whitespace-nowrap expression-overlay-label">
                                    Silakan Ikuti Instruksi</p>
                            </div>
                        </div>

                        <!-- Environment Info -->
                        <div class="absolute top-md right-md flex flex-col gap-xs items-end z-10">
                            <div
                                class="bg-black/60 text-white px-base py-xs rounded-lg text-xs backdrop-blur-md flex items-center gap-xs">
                                <span class="w-2 h-2 rounded-full bg-secondary-fixed animate-pulse"></span>
                                LIVE FEED
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Right Side: Status & Logs -->
                <aside class="lg:col-span-3 flex flex-col gap-md">
                    <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant h-full">
                        <h3 class="font-label-md text-label-md text-outline uppercase tracking-wider mb-md">Status Kehadiran
                        </h3>
                        <div class="space-y-md">
                            <!-- User Identity -->
                            <div
                                class="flex flex-col items-center p-md bg-surface-container-low rounded-xl border border-primary/10">
                                <div
                                    class="w-20 h-20 rounded-full bg-outline-variant mb-sm overflow-hidden border-2 border-surface-container-lowest flex items-center justify-center">
                                    <span class="material-symbols-outlined text-outline text-4xl">account_circle</span>
                                </div>
                                <p class="font-label-md text-label-md text-primary status-text">Mendeteksi Wajah...</p>
                            </div>
                            <div class="pt-base border-t border-outline-variant">
                                <h4 class="font-label-sm text-label-sm text-outline-variant mb-sm">Log Terdeteksi</h4>
                                <div
                                    class="ekspresi-terdeteksi text-body-sm text-on-surface-variant font-medium p-sm bg-surface-container-low rounded-lg">
                                    Menunggu deteksi wajah...
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </section>

    <!-- Interactive Layer: Processing Modal -->
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[100] hidden items-center justify-center" id="processingModal">
        <div
            class="bg-surface rounded-2xl p-xl max-w-sm w-full mx-md text-center shadow-2xl flex flex-col items-center gap-md">
            <div class="relative flex items-center justify-center">
                <div class="w-16 h-16 border-4 border-surface-container-high border-t-primary rounded-full animate-spin">
                </div>
                <span
                    class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-primary text-3xl">face</span>
            </div>
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface modal-title">Memverifikasi Wajah</h2>
                <p class="text-on-surface-variant mt-xs modal-description">Mohon tunggu sebentar, sistem sedang memvalidasi
                    data Anda.</p>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/faceapi/face-api.min.js"></script>
    <script src="/faceapi/scripts.js"></script>
    <script>
        const absenImages = [
            {
                src: '/assets/images/landing/expression/smile.png',
                label: 'Senyum'
            },
            {
                src: '/assets/images/landing/expression/flat.png',
                label: 'Datar'
            },
            {
                src: '/assets/images/landing/expression/gloomy.png',
                label: 'Murung'
            }
        ];

        const randomIndex = Math.floor(Math.random() * absenImages.length);
        const selected = absenImages[randomIndex];

        document.addEventListener('DOMContentLoaded', function () {
            // Fill target expression details
            const box = document.querySelector('.expression-box');
            if (box) {
                box.innerHTML = `<img src="${selected.src}" alt="${selected.label}" class="w-full h-full object-contain">`;
            }

            const label = document.querySelector('.expression-label');
            if (label) {
                label.textContent = selected.label;
            }

            const overlayLabel = document.querySelector('.expression-overlay-label');
            if (overlayLabel) {
                overlayLabel.textContent = `Silakan Ekspresi ${selected.label}`;
            }

            // Update description based on random expression
            const desc = document.querySelector('.expression-desc');
            if (desc) {
                desc.textContent = `Tirukan ekspresi "${selected.label}" pada gambar di atas`;
            }

            // Start camera
            const video = document.getElementById("video");
            const canvas = document.getElementById("canvas");
            const cameraLoading = document.getElementById("cameraLoading");
            const cameraContainer = document.getElementById("cameraContainer");

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

            // Save targeted expression
            const ekspresiHarus = selected.label.toLowerCase(); // e.g. "senyum", "datar", "murung"
            const ekspresiMap = {
                'senyum': 'happy',
                'datar': 'neutral',
                'murung': 'sad'
            };
            const ekspresiTarget = ekspresiMap[ekspresiHarus] || 'neutral';

            let absenBerhasil = false;

            // Callback from scripts.js
            window.absenEkspresiCheck = function (ekspresiTerdeteksi, confidence, faceLabel) {
                if (absenBerhasil) return;

                // Show status on the right side
                const statusTxt = document.querySelector('.status-text');
                const ekspresiDet = document.querySelector('.ekspresi-terdeteksi');

                if (faceLabel === "unknown" || !faceLabel) {
                    if (statusTxt) statusTxt.textContent = "Wajah Tidak Dikenali";
                    if (ekspresiDet) {
                        ekspresiDet.innerHTML = "<span class='text-red-500 font-semibold'>Wajah tidak cocok. Absen ditolak.</span>";
                    }
                    return;
                }

                if (statusTxt) statusTxt.textContent = "Wajah Terdeteksi";
                if (ekspresiDet) {
                    ekspresiDet.innerHTML = `<span class='text-green-600 font-semibold'>Mencocokkan: ${faceLabel} (${(confidence * 100).toFixed(0)}%)</span>`;
                }

                // Lock submit
                absenBerhasil = true;

                // Show Verification Stepper and Modal
                const stepVerifyBadge = document.getElementById('step-verify-badge');
                const stepVerifyTitle = document.getElementById('step-verify-title');
                if (stepVerifyBadge) {
                    stepVerifyBadge.classList.remove('bg-surface-container', 'text-on-surface-variant');
                    stepVerifyBadge.classList.add('bg-primary', 'text-on-primary');
                }
                if (stepVerifyTitle) {
                    stepVerifyTitle.classList.remove('text-on-surface-variant');
                    stepVerifyTitle.classList.add('text-primary');
                }

                const processingModal = document.getElementById('processingModal');
                if (processingModal) {
                    processingModal.style.display = 'flex';
                }

                // Check location
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (pos) {
                        const schoolLat = -6.521976890944639;
                        const schoolLng = 106.80741031694744;
                        const radiusMeter = 100;
                        const userLat = pos.coords.latitude;
                        const userLng = pos.coords.longitude;
                        const distance = getDistanceFromLatLonInMeters(userLat, userLng, schoolLat, schoolLng);

                        if (distance > radiusMeter) {
                            absenBerhasil = false;
                            if (processingModal) processingModal.style.display = 'none';
                            stopCameraAndBack("Anda berada di luar radius sekolah (" + distance.toFixed(1) + " meter). Absen tidak bisa dilakukan.");
                            return;
                        }

                        // Proceed to submit
                        submitAbsen(userLat, userLng, faceLabel);
                    }, function (err) {
                        absenBerhasil = false;
                        if (processingModal) processingModal.style.display = 'none';
                        stopCameraAndBack("Izin lokasi diperlukan untuk melakukan absen.");
                    }, { enableHighAccuracy: true });
                } else {
                    absenBerhasil = false;
                    if (processingModal) processingModal.style.display = 'none';
                    stopCameraAndBack("Browser tidak mendukung geolokasi.");
                }
            };

            function stopCameraAndBack(message) {
                try {
                    const video = document.getElementById("video");
                    if (video && video.srcObject) {
                        video.srcObject.getTracks().forEach(track => track.stop());
                        video.srcObject = null;
                    }
                } catch (e) { }

                Swal.fire({
                    icon: 'error',
                    title: 'Absen Gagal',
                    text: message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = "/";
                });
            }

            function getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
                const R = 6371000;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a =
                    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }

            function submitAbsen(lat, lng, faceLabel) {
                const video = document.getElementById("video");
                const canvas = document.createElement("canvas");
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext("2d");
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const photoData = canvas.toDataURL("image/jpeg", 0.92);

                fetch("{{ route('feature.absen.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        ekspresi: ekspresiTarget,
                        lat: lat,
                        lng: lng,
                        photo: photoData,
                        face_label: faceLabel
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Mark Step 3 as completed
                            const stepDoneBadge = document.getElementById('step-done-badge');
                            const stepDoneTitle = document.getElementById('step-done-title');
                            if (stepDoneBadge) {
                                stepDoneBadge.classList.remove('bg-surface-container', 'text-on-surface-variant');
                                stepDoneBadge.classList.add('bg-primary', 'text-on-primary');
                            }
                            if (stepDoneTitle) {
                                stepDoneTitle.classList.remove('text-on-surface-variant');
                                stepDoneTitle.classList.add('text-primary');
                            }

                            // Success view in modal
                            const processingModal = document.getElementById('processingModal');
                            if (processingModal) {
                                processingModal.querySelector('.modal-title').textContent = 'Absen Berhasil!';
                                processingModal.querySelector('.modal-description').textContent = 'Terima kasih, kehadiran Anda telah tercatat.';

                                const loaderEl = processingModal.querySelector('.animate-spin');
                                if (loaderEl) loaderEl.classList.add('hidden');

                                const faceIcon = processingModal.querySelector('.material-symbols-outlined');
                                if (faceIcon) faceIcon.classList.add('hidden');

                                const successIcon = document.createElement('div');
                                successIcon.className = "success-icon-wrapper";
                                successIcon.innerHTML = `<div class="w-16 h-16 bg-secondary-container text-on-secondary-container rounded-full flex items-center justify-center"><span class="material-symbols-outlined text-[40px]">check_circle</span></div>`;
                                processingModal.querySelector('.relative').appendChild(successIcon);
                            }

                            setTimeout(() => {
                                window.location.href = "/";
                            }, 2000);
                        } else {
                            const processingModal = document.getElementById('processingModal');
                            if (processingModal) processingModal.style.display = 'none';

                            const ekspresiDet = document.querySelector('.ekspresi-terdeteksi');
                            if (ekspresiDet) {
                                ekspresiDet.innerHTML = "<span class='text-red-500 font-semibold'>Gagal absen: " + (data.message || '') + "</span>";
                            }
                            absenBerhasil = false;
                        }
                    })
                    .catch(() => {
                        const processingModal = document.getElementById('processingModal');
                        if (processingModal) processingModal.style.display = 'none';

                        const ekspresiDet = document.querySelector('.ekspresi-terdeteksi');
                        if (ekspresiDet) {
                            ekspresiDet.innerHTML = "<span class='text-red-500 font-semibold'>Gagal mengirim absen.</span>";
                        }
                        absenBerhasil = false;
                    });
            }
        });
    </script>
@endpush
