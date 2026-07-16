@extends("landing.layout.app", ["title" => "Verifikasi Wajah Izin"])

@push("style")
<style>
    .face-container {
        display: flex;
        gap: 2rem;
        margin-top: 2rem;
        margin-bottom: 2rem;
    }
    .face-left, .face-right {
        width: 50%;
        min-height: 400px;
        background: rgba(255,255,255,0.05);
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        backdrop-filter: blur(10px);
    }
    @media (max-width: 900px) {
        .face-container {
            flex-direction: column;
        }
        .face-left, .face-right {
            width: 100%;
            margin-bottom: 1.5rem;
        }
    }
    #cameraContainer {
        position: relative;
        width: 100%;
        height: 340px;
        max-width: 450px;
    }
    #video, #canvas {
        width: 100% !important;
        height: 100% !important;
        border-radius: 1rem;
        display: block;
    }
    #canvas {
        position: absolute;
        left: 0;
        top: 0;
        pointer-events: none;
    }
</style>
@endpush

@section("content")
<section class="min-h-screen text-slate-200">
    <div class="container mx-auto px-6 py-8">
        @include("landing.partials.header")
        <div class="text-center mb-10">
            <h1 class="text-2xl font-bold text-[#0b1c30] mb-2">Verifikasi Wajah Pengajuan Izin</h1>
            <p class="text-slate-400">Pastikan wajah Anda dikenali sebelum mengirim pengajuan izin.</p>
        </div>
        <div class="face-container">
            <!-- Kontainer Kiri -->
            <div class="face-left flex flex-col items-center justify-center">
                <div class="text-center px-4">
                    <h2 class="text-lg font-semibold text-[#0b1c30] mb-2">Arahkan wajah ke kamera</h2>
                    <p class="text-slate-400">Pastikan wajah Anda terlihat jelas di kamera.</p>
                    <div class="ekspresi-terdeteksi text-[#0b1c30] mt-2"></div>
                </div>
            </div>
            <!-- Kontainer Kanan -->
            <div class="face-right flex items-center justify-center">
                <div class="w-full h-full p-8 flex flex-col items-center justify-center">
                    <div id="cameraLoading" class="flex flex-col items-center justify-center w-full h-full">
                        <div class="loader mb-4"></div>
                        <div class="text-slate-400">Memulai kamera...</div>
                    </div>
                    <div id="cameraContainer" style="display:none; width:100%; height:100%;" class="flex flex-col items-center justify-center">
                        <video id="video" autoplay muted style="width:100%; height:100%; object-fit:cover; border-radius:1rem; background:#222; display:block;"></video>
                        <canvas id="canvas" style="width:100%; height:100%; position:absolute; left:0; top:0;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/faceapi/face-api.min.js"></script>
<script src="/faceapi/scripts.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");
    const cameraLoading = document.getElementById("cameraLoading");
    const cameraContainer = document.getElementById("cameraContainer");
    let izinData = null, izinFiles = null, izinFileNames = null;
    let submitInProgress = false;

    // Ambil data izin dari sessionStorage
    try {
        izinData = JSON.parse(sessionStorage.getItem('izinData'));
        izinFiles = JSON.parse(sessionStorage.getItem('izinFiles'));
        izinFileNames = JSON.parse(sessionStorage.getItem('izinFileNames'));
    } catch (e) {}

    if (!izinData || !izinFiles || !izinFileNames) {
        Swal.fire({ icon: 'error', title: 'Data tidak lengkap', text: 'Silakan isi form izin terlebih dahulu.' })
            .then(() => window.location.href = '/izin');
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
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Izin berhasil diajukan.',
                    timer: 2500,
                    showConfirmButton: false
                }).then(() => {
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
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: err.message || 'Gagal mengirim izin.',
            });
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
