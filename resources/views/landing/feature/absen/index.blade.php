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
        @include("landing.partials.header")

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
@endsection

@push('script')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- FaceAPI Scripts -->
    <script src="/faceapi/face-api.min.js"></script>
    <script src="/faceapi/scripts.js"></script>
    
    <!-- Face Detection Logic -->
    <script src="/js/face-detection.js"></script>
@endpush