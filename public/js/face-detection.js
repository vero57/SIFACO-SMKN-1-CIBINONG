// face-detection.js
const absenImages = [
    {
        src: "/assets/images/landing/expression/smile.png",
        label: "Senyum",
    },
    {
        src: "/assets/images/landing/expression/flat.png",
        label: "Datar",
    },
    {
        src: "/assets/images/landing/expression/gloomy.png",
        label: "Murung",
    },
];

class FaceDetection {
    constructor() {
        this.randomIndex = Math.floor(Math.random() * absenImages.length);
        this.selected = absenImages[this.randomIndex];
        this.ekspresiTarget = this.getEkspresiTarget();
        this.absenBerhasil = false;
        this.video = null;
        this.canvas = null;
        this.processingModal = null;
    }

    getEkspresiTarget() {
        const ekspresiHarus = this.selected.label.toLowerCase();
        const ekspresiMap = {
            senyum: "happy",
            datar: "neutral",
            murung: "sad",
        };
        return ekspresiMap[ekspresiHarus] || "neutral";
    }

    init() {
        this.setupExpressionUI();
        this.startCamera();
        this.setupGlobalCallback();
    }

    setupExpressionUI() {
        // Fill target expression details
        const box = document.querySelector(".expression-box");
        if (box) {
            box.innerHTML = `<img src="${this.selected.src}" alt="${this.selected.label}" class="w-full h-full object-contain">`;
        }

        const label = document.querySelector(".expression-label");
        if (label) {
            label.textContent = this.selected.label;
        }

        const overlayLabel = document.querySelector(
            ".expression-overlay-label",
        );
        if (overlayLabel) {
            overlayLabel.textContent = `Silakan Ekspresi ${this.selected.label}`;
        }

        const desc = document.querySelector(".expression-desc");
        if (desc) {
            desc.textContent = `Tirukan ekspresi "${this.selected.label}" pada gambar di atas`;
        }
    }

    startCamera() {
        this.video = document.getElementById("video");
        this.canvas = document.getElementById("canvas");
        const cameraLoading = document.getElementById("cameraLoading");
        const cameraContainer = document.getElementById("cameraContainer");
        this.processingModal = document.getElementById("processingModal");

        navigator.mediaDevices
            .getUserMedia({ video: {} })
            .then((stream) => {
                this.video.srcObject = stream;
                this.video.onloadedmetadata = () => {
                    cameraLoading.style.display = "none";
                    cameraContainer.style.display = "flex";
                    this.canvas.width = this.video.videoWidth;
                    this.canvas.height = this.video.videoHeight;
                };
            })
            .catch((err) => {
                cameraLoading.innerHTML =
                    "<div class='text-red-500'>Gagal mengakses kamera: " +
                    err.message +
                    "</div>";
            });
    }

    setupGlobalCallback() {
        const self = this;
        window.absenEkspresiCheck = function (
            ekspresiTerdeteksi,
            confidence,
            faceLabel,
        ) {
            self.handleFaceDetection(ekspresiTerdeteksi, confidence, faceLabel);
        };
    }

    handleFaceDetection(ekspresiTerdeteksi, confidence, faceLabel) {
        if (this.absenBerhasil) return;

        const statusTxt = document.querySelector(".status-text");
        const ekspresiDet = document.querySelector(".ekspresi-terdeteksi");

        if (faceLabel === "unknown" || !faceLabel) {
            if (statusTxt) statusTxt.textContent = "Wajah Tidak Dikenali";
            if (ekspresiDet) {
                ekspresiDet.innerHTML =
                    "<span class='text-red-500 font-semibold'>Wajah tidak cocok. Absen ditolak.</span>";
            }
            return;
        }

        if (statusTxt) statusTxt.textContent = "Wajah Terdeteksi";
        if (ekspresiDet) {
            ekspresiDet.innerHTML = `<span class='text-green-600 font-semibold'>Mencocokkan: ${faceLabel} (${(confidence * 100).toFixed(0)}%)</span>`;
        }

        this.absenBerhasil = true;
        this.updateStepper();
        this.showProcessingModal();

        // Check location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => this.handleLocationSuccess(pos, faceLabel),
                (err) => this.handleLocationError(err),
                { enableHighAccuracy: true },
            );
        } else {
            this.absenBerhasil = false;
            if (this.processingModal)
                this.processingModal.style.display = "none";
            this.stopCameraAndBack("Browser tidak mendukung geolokasi.");
        }
    }

    handleLocationSuccess(pos, faceLabel) {
        const schoolLat = -6.521976890944639;
        const schoolLng = 106.80741031694744;
        const radiusMeter = 100;
        const userLat = pos.coords.latitude;
        const userLng = pos.coords.longitude;
        const distance = this.getDistanceFromLatLonInMeters(
            userLat,
            userLng,
            schoolLat,
            schoolLng,
        );

        if (distance > radiusMeter) {
            this.absenBerhasil = false;
            if (this.processingModal)
                this.processingModal.style.display = "none";
            this.stopCameraAndBack(
                "Anda berada di luar radius sekolah (" +
                    distance.toFixed(1) +
                    " meter). Absen tidak bisa dilakukan.",
            );
            return;
        }

        this.submitAbsen(userLat, userLng, faceLabel);
    }

    handleLocationError(err) {
        this.absenBerhasil = false;
        if (this.processingModal) this.processingModal.style.display = "none";
        this.stopCameraAndBack("Izin lokasi diperlukan untuk melakukan absen.");
    }

    updateStepper() {
        const stepVerifyBadge = document.getElementById("step-verify-badge");
        const stepVerifyTitle = document.getElementById("step-verify-title");

        if (stepVerifyBadge) {
            stepVerifyBadge.classList.remove(
                "bg-surface-container",
                "text-on-surface-variant",
            );
            stepVerifyBadge.classList.add("bg-primary", "text-on-primary");
        }
        if (stepVerifyTitle) {
            stepVerifyTitle.classList.remove("text-on-surface-variant");
            stepVerifyTitle.classList.add("text-primary");
        }
    }

    showProcessingModal() {
        if (this.processingModal) {
            this.processingModal.style.display = "flex";
        }
    }

    getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = ((lat2 - lat1) * Math.PI) / 180;
        const dLon = ((lon2 - lon1) * Math.PI) / 180;
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos((lat1 * Math.PI) / 180) *
                Math.cos((lat2 * Math.PI) / 180) *
                Math.sin(dLon / 2) *
                Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    showErrorModal(message, redirect = true) {
        if (this.processingModal) {
            const modalTitle =
                this.processingModal.querySelector(".modal-title");
            const modalDesc =
                this.processingModal.querySelector(".modal-description");
            const loaderEl =
                this.processingModal.querySelector(".animate-spin");
            const faceIcon = this.processingModal.querySelector(
                ".material-symbols-outlined",
            );
            const successIconWrapper = this.processingModal.querySelector(
                ".success-icon-wrapper",
            );

            if (modalTitle) {
                modalTitle.textContent = "Absen Gagal";
                modalTitle.className =
                    "font-headline-md text-headline-md modal-error-title";
            }
            if (modalDesc) {
                modalDesc.textContent = message;
                modalDesc.className =
                    "text-on-surface-variant mt-xs modal-description";
                modalDesc.style.color = "#dc2626";
            }

            if (loaderEl) {
                loaderEl.classList.add("hidden");
            }
            if (faceIcon) {
                faceIcon.textContent = "error";
                faceIcon.className =
                    "material-symbols-outlined absolute inset-0 flex items-center justify-center modal-error-icon";
            }

            if (successIconWrapper) {
                successIconWrapper.remove();
            }

            this.processingModal.style.display = "flex";

            const oldBtn =
                this.processingModal.querySelector(".modal-close-btn");
            if (oldBtn) oldBtn.remove();

            const closeButton = document.createElement("button");
            closeButton.className = "modal-close-btn";
            closeButton.textContent = "Kembali";
            closeButton.onclick = () => {
                this.processingModal.style.display = "none";
                if (redirect) {
                    window.location.href = "/";
                }
            };

            this.processingModal
                .querySelector(".flex.flex-col")
                .appendChild(closeButton);
        }
    }

    stopCameraAndBack(message) {
        try {
            if (this.video && this.video.srcObject) {
                this.video.srcObject
                    .getTracks()
                    .forEach((track) => track.stop());
                this.video.srcObject = null;
            }
        } catch (e) {}

        this.showErrorModal(message, true);
    }

    submitAbsen(lat, lng, faceLabel) {
        const canvas = document.createElement("canvas");
        canvas.width = this.video.videoWidth;
        canvas.height = this.video.videoHeight;
        const ctx = canvas.getContext("2d");
        ctx.drawImage(this.video, 0, 0, canvas.width, canvas.height);
        const photoData = canvas.toDataURL("image/jpeg", 0.92);

        fetch("/feature/absen/store", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
            },
            body: JSON.stringify({
                ekspresi: this.ekspresiTarget,
                lat: lat,
                lng: lng,
                photo: photoData,
                face_label: faceLabel,
            }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    this.handleSuccessSubmit();
                } else {
                    this.handleFailedSubmit(data.message);
                }
            })
            .catch(() => {
                this.handleErrorSubmit();
            });
    }

    handleSuccessSubmit() {
        const stepDoneBadge = document.getElementById("step-done-badge");
        const stepDoneTitle = document.getElementById("step-done-title");

        if (stepDoneBadge) {
            stepDoneBadge.classList.remove(
                "bg-surface-container",
                "text-on-surface-variant",
            );
            stepDoneBadge.classList.add("bg-primary", "text-on-primary");
        }
        if (stepDoneTitle) {
            stepDoneTitle.classList.remove("text-on-surface-variant");
            stepDoneTitle.classList.add("text-primary");
        }

        if (this.processingModal) {
            const modalTitle =
                this.processingModal.querySelector(".modal-title");
            const modalDesc =
                this.processingModal.querySelector(".modal-description");
            const loaderEl =
                this.processingModal.querySelector(".animate-spin");
            const faceIcon = this.processingModal.querySelector(
                ".material-symbols-outlined",
            );

            if (modalTitle) {
                modalTitle.textContent = "Absen Berhasil!";
                modalTitle.className =
                    "font-headline-md text-headline-md text-green-600";
            }
            if (modalDesc) {
                modalDesc.textContent =
                    "Terima kasih, kehadiran Anda telah tercatat.";
                modalDesc.style.color = "";
            }

            if (loaderEl) loaderEl.classList.add("hidden");
            if (faceIcon) faceIcon.classList.add("hidden");

            const successIcon = document.createElement("div");
            successIcon.className = "success-icon-wrapper";
            successIcon.innerHTML = `<div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center"><span class="material-symbols-outlined text-[40px]">check_circle</span></div>`;
            this.processingModal
                .querySelector(".relative")
                .appendChild(successIcon);
        }

        setTimeout(() => {
            window.location.href = "/";
        }, 2000);
    }

    handleFailedSubmit(message) {
        if (this.processingModal) this.processingModal.style.display = "none";

        const ekspresiDet = document.querySelector(".ekspresi-terdeteksi");
        if (ekspresiDet) {
            ekspresiDet.innerHTML =
                "<span class='text-red-500 font-semibold'>Gagal absen: " +
                (message || "") +
                "</span>";
        }
        this.absenBerhasil = false;
        this.showErrorModal(message || "Absen gagal, silakan coba lagi.", true);
    }

    handleErrorSubmit() {
        if (this.processingModal) this.processingModal.style.display = "none";

        const ekspresiDet = document.querySelector(".ekspresi-terdeteksi");
        if (ekspresiDet) {
            ekspresiDet.innerHTML =
                "<span class='text-red-500 font-semibold'>Gagal mengirim absen.</span>";
        }
        this.absenBerhasil = false;
        this.showErrorModal(
            "Gagal mengirim data absen. Silakan coba lagi.",
            true,
        );
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    const faceDetection = new FaceDetection();
    faceDetection.init();
});
