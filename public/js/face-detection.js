// face-detection.js
// Konfigurasi sekolah dibaca dari window.schoolConfig yang diinjek oleh server (Blade).
// Jangan hardcode koordinat atau radius di sini!

/**
 * Gambar ekspresi yang dipilih secara acak sebagai challenge untuk anti-spoofing.
 * Path menggunakan asset lokal agar tidak bergantung URL eksternal.
 */
const absenImages = [
    { src: "/assets/images/landing/expression/smile.png", label: "Senyum" },
    { src: "/assets/images/landing/expression/flat.png",  label: "Datar"  },
    { src: "/assets/images/landing/expression/gloomy.png",label: "Murung" },
];

class FaceDetection {
    constructor() {
        this.randomIndex    = Math.floor(Math.random() * absenImages.length);
        this.selected       = absenImages[this.randomIndex];
        this.ekspresiTarget = this.getEkspresiTarget();
        this.absenBerhasil  = false;
        this.video          = null;
        this.canvas         = null;
        this.processingModal= null;
        this.lastCheckTime  = 0; 
        this.holdingTimer   = null;
        this.holdingCount   = 0;
        this.REQUIRED_HOLD_FRAMES = 4; 
        this.MIN_CONFIDENCE = 0.50; 
        this.capturedPhoto  = null;
    }

    getEkspresiTarget() {
        const map = { senyum: "happy", datar: "neutral", murung: "sad" };
        return map[this.selected.label.toLowerCase()] || "neutral";
    }

    init() {
        this.setupExpressionUI();
        this.startCamera();
        this.setupGlobalCallback();
    }

    setupExpressionUI() {
        const box = document.querySelector(".expression-box");
        if (box) {
            box.innerHTML = `<img src="${this.selected.src}" alt="${this.selected.label}" class="w-full h-full object-contain">`;
        }

        const label = document.querySelector(".expression-label");
        if (label) label.textContent = this.selected.label;

        const overlayLabel = document.querySelector(".expression-overlay-label");
        if (overlayLabel) overlayLabel.textContent = `Silakan Ekspresi ${this.selected.label}`;

        const desc = document.querySelector(".expression-desc");
        if (desc) desc.textContent = `Tirukan ekspresi "${this.selected.label}" pada gambar di atas`;
    }

    startCamera() {
        this.video          = document.getElementById("video");
        this.canvas         = document.getElementById("canvas");
        this.processingModal= document.getElementById("processingModal");

        const cameraLoading   = document.getElementById("cameraLoading");
        const cameraContainer = document.getElementById("cameraContainer");

        // Batasi resolusi & FPS agar tidak memberatkan CPU
        const constraints = {
            video: {
                width:     { ideal: 640 },
                height:    { ideal: 480 },
                frameRate: { ideal: 24, max: 30 },
            },
        };

        navigator.mediaDevices
            .getUserMedia(constraints)
            .then((stream) => {
                this.video.srcObject = stream;
                this.video.onloadedmetadata = () => {
                    cameraLoading.style.display   = "none";
                    cameraContainer.style.display = "flex";
                    this.canvas.width  = this.video.videoWidth;
                    this.canvas.height = this.video.videoHeight;
                };
            })
            .catch((err) => {
                cameraLoading.innerHTML = `<div class='text-red-500'>Gagal mengakses kamera: ${err.message}</div>`;
            });
    }

    setupGlobalCallback() {
        const self = this;
        window.absenEkspresiCheck = function (ekspresiTerdeteksi, confidence, faceLabel) {
            // Throttle: minimal 250ms antar pemrosesan
            const now = Date.now();
            if (now - self.lastCheckTime < 250) return;
            self.lastCheckTime = now;

            self.handleFaceDetection(ekspresiTerdeteksi, confidence, faceLabel);
        };
    }

    handleFaceDetection(ekspresiTerdeteksi, confidence, faceLabel) {
        if (this.absenBerhasil) return;

        const statusTxt  = document.querySelector(".status-text");
        const ekspresiDet= document.querySelector(".ekspresi-terdeteksi");

        if (faceLabel === "unknown" || !faceLabel) {
            this.holdingCount = 0;
            if (statusTxt)   statusTxt.textContent = "Wajah Tidak Dikenali";
            if (ekspresiDet) ekspresiDet.innerHTML = "<span class='text-red-500 font-semibold'>Wajah tidak cocok. Absen ditolak.</span>";
            return;
        }

        // Cek 1: Apakah ekspresi terdeteksi cocok dengan ekspresi target yang diperintahkan?
        const isMatchTarget = (ekspresiTerdeteksi === this.ekspresiTarget);
        // Cek 2: Apakah confidence memenuhi threshold minimum?
        const isSufficientConfidence = (confidence >= this.MIN_CONFIDENCE);

        if (!isMatchTarget || !isSufficientConfidence) {
            this.holdingCount = 0; // Reset hitungan jika ekspresi salah / berubah
            if (statusTxt) statusTxt.textContent = "Ekspresi Belum Sesuai";
            if (ekspresiDet) {
                ekspresiDet.innerHTML = `<span class='text-amber-600 font-semibold'>Tunjukkan ekspresi <b>${this.selected.label}</b> (Terdeteksi: ${ekspresiTerdeteksi || '-'})</span>`;
            }
            return;
        }

        // Ekspresi sesuai! Naikkan counter tahan ekspresi (anti-accidental match)
        this.holdingCount++;

        if (statusTxt) statusTxt.textContent = `Tahan Ekspresi... (${this.holdingCount}/${this.REQUIRED_HOLD_FRAMES})`;
        if (ekspresiDet) {
            ekspresiDet.innerHTML = `<span class='text-green-600 font-semibold'>Ekspresi Cocok! Tahan sebentar... (${(confidence * 100).toFixed(0)}%)</span>`;
        }

        // Belum mencapai durasi tahan yang cukup
        if (this.holdingCount < this.REQUIRED_HOLD_FRAMES) {
            return;
        }

        // Kunci & matikan kamera setelah ekpresi terbukti ditahan & cocok
        this.absenBerhasil = true;
        this.capturedPhoto = this.captureCurrentFrame();
        this.stopCameraStreamOnly();
        this.updateStepper();
        this.showProcessingModal();

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => this.handleLocationSuccess(pos, faceLabel),
                ()    => this.handleLocationError(),
                { enableHighAccuracy: true },
            );
        } else {
            this.absenBerhasil = false;
            this.hideProcessingModal();
            this.stopCameraAndBack("Browser tidak mendukung geolokasi.");
        }
    }

    stopCameraStreamOnly() {
        if (this.video && this.video.srcObject) {
            this.video.srcObject.getTracks().forEach((track) => track.stop());
            this.video.srcObject = null;
        }
    }

    captureCurrentFrame() {
        if (!this.video || !this.video.videoWidth || !this.video.videoHeight) {
            return null;
        }

        const canvas = document.createElement("canvas");
        canvas.width = this.video.videoWidth;
        canvas.height = this.video.videoHeight;

        const ctx = canvas.getContext("2d");
        ctx.drawImage(this.video, 0, 0, canvas.width, canvas.height);

        return canvas.toDataURL("image/jpeg", 0.8);
    }

    hideProcessingModal() {
        if (this.processingModal) this.processingModal.style.display = "none";
    }

    /**
     * Validasi lokasi menggunakan koordinat dari window.schoolConfig (diinjek server).
     * Formula Haversine dijalankan di sisi client sebagai UX cepat;
     * server juga memvalidasi ulang untuk keamanan.
     */
    handleLocationSuccess(pos, faceLabel) {
        const cfg = window.schoolConfig || { lat: 0, lng: 0, radius: 100 };

        const userLat  = pos.coords.latitude;
        const userLng  = pos.coords.longitude;
        const distance = this.getDistanceFromLatLonInMeters(userLat, userLng, cfg.lat, cfg.lng);

        if (distance > cfg.radius) {
            this.absenBerhasil = false;
            this.hideProcessingModal();
            this.stopCameraAndBack(
                `Anda berada di luar radius sekolah (${distance.toFixed(1)} meter dari pusat sekolah). Absen tidak dapat dilakukan.`
            );
            return;
        }

        this.submitAbsen(userLat, userLng, faceLabel);
    }

    handleLocationError() {
        this.absenBerhasil = false;
        this.hideProcessingModal();
        this.stopCameraAndBack("Izin lokasi diperlukan untuk melakukan absen.");
    }

    updateStepper() {
        const stepVerifyBadge = document.getElementById("step-verify-badge");
        const stepVerifyTitle = document.getElementById("step-verify-title");

        if (stepVerifyBadge) {
            stepVerifyBadge.classList.remove("bg-surface-container", "text-on-surface-variant");
            stepVerifyBadge.classList.add("bg-primary", "text-on-primary");
        }
        if (stepVerifyTitle) {
            stepVerifyTitle.classList.remove("text-on-surface-variant");
            stepVerifyTitle.classList.add("text-primary");
        }
    }

    showProcessingModal() {
        if (this.processingModal) this.processingModal.style.display = "flex";
    }

    /** Formula Haversine — menghitung jarak dua titik koordinat dalam meter. */
    getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
        const R    = 6371000;
        const dLat = ((lat2 - lat1) * Math.PI) / 180;
        const dLon = ((lon2 - lon1) * Math.PI) / 180;
        const a    =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos((lat1 * Math.PI) / 180) *
            Math.cos((lat2 * Math.PI) / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    showErrorModal(message, redirect = true) {
        if (!this.processingModal) return;

        const modalTitle         = this.processingModal.querySelector(".modal-title");
        const modalDesc          = this.processingModal.querySelector(".modal-description");
        const loaderEl           = this.processingModal.querySelector(".animate-spin");
        const faceIcon           = this.processingModal.querySelector(".material-symbols-outlined");
        const successIconWrapper = this.processingModal.querySelector(".success-icon-wrapper");

        if (modalTitle) {
            modalTitle.textContent = "Absen Gagal";
            modalTitle.className   = "font-headline-md text-headline-md modal-error-title";
        }
        if (modalDesc) {
            modalDesc.textContent    = message;
            modalDesc.className      = "text-on-surface-variant mt-xs modal-description";
            modalDesc.style.color    = "#dc2626";
        }
        if (loaderEl)           loaderEl.classList.add("hidden");
        if (faceIcon) {
            faceIcon.textContent = "error";
            faceIcon.className   = "material-symbols-outlined absolute inset-0 flex items-center justify-center modal-error-icon";
        }
        if (successIconWrapper) successIconWrapper.remove();

        this.processingModal.style.display = "flex";

        // Hapus tombol lama lalu buat baru
        const oldBtn = this.processingModal.querySelector(".modal-close-btn");
        if (oldBtn) oldBtn.remove();

        const closeButton    = document.createElement("button");
        closeButton.className= "modal-close-btn";
        closeButton.textContent = "Kembali";
        closeButton.onclick  = () => {
            this.hideProcessingModal();
            if (redirect) window.location.href = "/";
        };
        this.processingModal.querySelector(".flex.flex-col").appendChild(closeButton);
    }

    stopCameraAndBack(message) {
        this.stopCameraStreamOnly();
        this.showErrorModal(message, true);
    }

    submitAbsen(lat, lng, faceLabel) {
        // Gunakan URL dari window.absenStoreUrl yang diinjek server (bukan hardcode "/feature/absen/store")
        const storeUrl = window.absenStoreUrl || "/absen/store";

        fetch(storeUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":  document.querySelector('meta[name="csrf-token"]')?.content || "",
            },
            body: JSON.stringify({
                ekspresi:   this.ekspresiTarget,
                lat:        lat,
                lng:        lng,
                photo:      this.capturedPhoto,
                face_label: faceLabel,
            }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    this.handleSuccessSubmit(data.status);
                } else {
                    this.handleFailedSubmit(data.message);
                }
            })
            .catch(() => {
                this.handleErrorSubmit();
            });
    }

    handleSuccessSubmit(statusAbsen) {
        const stepDoneBadge = document.getElementById("step-done-badge");
        const stepDoneTitle = document.getElementById("step-done-title");

        if (stepDoneBadge) {
            stepDoneBadge.classList.remove("bg-surface-container", "text-on-surface-variant");
            stepDoneBadge.classList.add("bg-primary", "text-on-primary");
        }
        if (stepDoneTitle) {
            stepDoneTitle.classList.remove("text-on-surface-variant");
            stepDoneTitle.classList.add("text-primary");
        }

        if (this.processingModal) {
            const modalTitle = this.processingModal.querySelector(".modal-title");
            const modalDesc  = this.processingModal.querySelector(".modal-description");
            const loaderEl   = this.processingModal.querySelector(".animate-spin");
            const faceIcon   = this.processingModal.querySelector(".material-symbols-outlined");

            if (modalTitle) {
                modalTitle.textContent = "Absen Berhasil!";
                modalTitle.className   = "font-headline-md text-headline-md text-green-600";
            }
            if (modalDesc) {
                const statusText = statusAbsen ? ` Status: ${statusAbsen}.` : "";
                modalDesc.textContent = `Terima kasih, kehadiran Anda telah tercatat.${statusText}`;
                modalDesc.style.color = "";
            }
            if (loaderEl)  loaderEl.classList.add("hidden");
            if (faceIcon)  faceIcon.classList.add("hidden");

            const successIcon = document.createElement("div");
            successIcon.className = "success-icon-wrapper";
            successIcon.innerHTML = `<div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center"><span class="material-symbols-outlined text-[40px]">check_circle</span></div>`;
            this.processingModal.querySelector(".relative").appendChild(successIcon);
        }

        setTimeout(() => { window.location.href = "/"; }, 2000);
    }

    handleFailedSubmit(message) {
        this.hideProcessingModal();

        const ekspresiDet = document.querySelector(".ekspresi-terdeteksi");
        if (ekspresiDet) {
            ekspresiDet.innerHTML = `<span class='text-red-500 font-semibold'>Gagal absen: ${message || ""}</span>`;
        }
        this.absenBerhasil = false;
        this.showErrorModal(message || "Absen gagal, silakan coba lagi.", true);
    }

    handleErrorSubmit() {
        this.hideProcessingModal();

        const ekspresiDet = document.querySelector(".ekspresi-terdeteksi");
        if (ekspresiDet) {
            ekspresiDet.innerHTML = "<span class='text-red-500 font-semibold'>Gagal mengirim data absen.</span>";
        }
        this.absenBerhasil = false;
        this.showErrorModal("Gagal mengirim data absen. Periksa koneksi internet Anda.", true);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const faceDetection = new FaceDetection();
    faceDetection.init();
});
