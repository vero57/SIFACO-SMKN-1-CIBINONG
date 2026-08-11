/**
 * Custom Alert Utility dengan Design dari alert.blade.php
 * Menyediakan interface yang konsisten untuk menampilkan berbagai tipe notifikasi
 */

// Icon mapping untuk berbagai tipe alert
const iconMap = {
    success: '<i class="fas fa-check-circle text-green-500" style="font-size: 48px;"></i>',
    error: '<i class="fas fa-times-circle text-red-500" style="font-size: 48px;"></i>',
    warning: '<i class="fas fa-exclamation-circle text-yellow-500" style="font-size: 48px;"></i>',
    info: '<i class="fas fa-info-circle text-blue-500" style="font-size: 48px;"></i>',
    question: '<i class="fas fa-question-circle text-blue-500" style="font-size: 48px;"></i>',
};

/**
 * Membuat alert modal dengan design custom
 * @param {string} type - Tipe alert: success, error, warning, info
 * @param {string} title - Judul alert
 * @param {string} message - Pesan alert
 * @param {number} timer - Waktu auto close (0 = tidak auto close)
 * @param {boolean} showConfirm - Tampilkan tombol OK
 */
const createAlert = (type = 'info', title = 'Alert', message = '', timer = 3000, showConfirm = true) => {
    // Buat container jika belum ada
    let container = document.getElementById('custom-alert-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'custom-alert-container';
        document.body.appendChild(container);
    }

    // Buat unique ID untuk alert
    const alertId = 'alert-' + Date.now();

    const icon = iconMap[type] || iconMap.info;

    const alertHTML = `
        <div id="${alertId}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: flex;">
            <div class="relative w-[560px] rounded-[20px] bg-gradient-to-r from-slate-700 to-neutral-400 p-6 font-sans shadow-lg" style="background: linear-gradient(to right, #74aabf, #3986a3) top/100% 6px no-repeat, white; border-radius: 20px; background-clip: padding-box, border-box;">
                <div class="mx-auto h-[83px] w-[83px] flex items-center justify-center">
                    ${icon}
                </div>

                <h2 class="mt-4 text-center font-sans text-2xl font-bold text-stone-900">
                    ${title}
                </h2>

                <p class="mt-2 text-center text-base font-medium text-black">
                    ${message}
                </p>

                <div class="mt-6 flex justify-center">
                    <button class="alert-close-btn rounded-[5px] bg-gradient-to-r from-[#74AABF] to-[#3986A3] px-10 py-2 font-medium text-white cursor-pointer">OK</button>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', alertHTML);

    const alertElement = document.getElementById(alertId);
    const closeBtn = alertElement.querySelector('.alert-close-btn');

    // Close button handler
    const closeAlert = () => {
        alertElement.style.display = 'none';
        alertElement.remove();
    };

    closeBtn.addEventListener('click', closeAlert);

    // Auto close jika ada timer
    if (timer > 0) {
        setTimeout(closeAlert, timer);
    }

    return {
        element: alertElement,
        close: closeAlert
    };
};

/**
 * Menampilkan alert sukses
 * @param {string} message - Pesan yang akan ditampilkan
 * @param {string} title - Judul alert (default: 'Sukses!')
 * @param {number} timer - Durasi tampil dalam ms (default: 3000)
 */
const showSuccess = (message, title = 'Sukses!', timer = 3000) => {
    return createAlert('success', title, message, timer, true);
};

/**
 * Menampilkan alert error
 * @param {string} message - Pesan yang akan ditampilkan
 * @param {string} title - Judul alert (default: 'Error!')
 * @param {number} timer - Durasi tampil dalam ms (default: 4000)
 */
const showError = (message, title = 'Error!', timer = 4000) => {
    return createAlert('error', title, message, timer, true);
};

/**
 * Menampilkan alert warning
 * @param {string} message - Pesan yang akan ditampilkan
 * @param {string} title - Judul alert (default: 'Peringatan!')
 * @param {number} timer - Durasi tampil dalam ms (default: 3500)
 */
const showWarning = (message, title = 'Peringatan!', timer = 3500) => {
    return createAlert('warning', title, message, timer, true);
};

/**
 * Menampilkan alert info
 * @param {string} message - Pesan yang akan ditampilkan
 * @param {string} title - Judul alert (default: 'Informasi')
 * @param {number} timer - Durasi tampil dalam ms (default: 3000)
 */
const showInfo = (message, title = 'Informasi', timer = 3000) => {
    return createAlert('info', title, message, timer, true);
};

/**
 * Menampilkan konfirmasi dialog dengan tombol Ya/Tidak
 * @param {string} message - Pesan konfirmasi
 * @param {string} title - Judul dialog (default: 'Konfirmasi')
 * @param {Function} onConfirm - Callback ketika user klik "Ya"
 * @param {Function} onCancel - Callback ketika user klik "Tidak" (opsional)
 */
const showConfirm = (message, title = 'Konfirmasi', onConfirm = null, onCancel = null) => {
    // Buat container jika belum ada
    let container = document.getElementById('custom-alert-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'custom-alert-container';
        document.body.appendChild(container);
    }

    const alertId = 'alert-' + Date.now();
    const icon = iconMap.question;

    const alertHTML = `
        <div id="${alertId}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="relative w-[560px] rounded-[20px] bg-gradient-to-r from-slate-700 to-neutral-400 p-6 font-sans shadow-lg" style="background: linear-gradient(to right, #74aabf, #3986a3) top/100% 6px no-repeat, white; border-radius: 20px; background-clip: padding-box, border-box;">
                <div class="mx-auto h-[83px] w-[83px] flex items-center justify-center">
                    ${icon}
                </div>

                <h2 class="mt-4 text-center font-sans text-2xl font-bold text-stone-900">
                    ${title}
                </h2>

                <p class="mt-2 text-center text-base font-medium text-black">
                    ${message}
                </p>

                <div class="mt-6 flex justify-center gap-4">
                    <button class="alert-cancel-btn rounded-lg border border-stone-300 px-6 py-2 text-stone-700 cursor-pointer font-medium">Tidak</button>
                    <button class="alert-confirm-btn rounded-[5px] bg-gradient-to-r from-[#74AABF] to-[#3986A3] px-6 py-2 font-medium text-white cursor-pointer">Ya</button>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', alertHTML);

    const alertElement = document.getElementById(alertId);
    const confirmBtn = alertElement.querySelector('.alert-confirm-btn');
    const cancelBtn = alertElement.querySelector('.alert-cancel-btn');

    const closeAlert = () => {
        alertElement.style.display = 'none';
        alertElement.remove();
    };

    confirmBtn.addEventListener('click', () => {
        closeAlert();
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
    });

    cancelBtn.addEventListener('click', () => {
        closeAlert();
        if (typeof onCancel === 'function') {
            onCancel();
        }
    });

    return {
        element: alertElement,
        close: closeAlert
    };
};

/**
 * Menampilkan loading alert dengan spinner
 * @param {string} message - Pesan yang akan ditampilkan
 * @param {string} title - Judul alert (default: 'Mohon tunggu')
 */
const showLoading = (message = 'Memproses...', title = 'Mohon tunggu') => {
    // Buat container jika belum ada
    let container = document.getElementById('custom-alert-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'custom-alert-container';
        document.body.appendChild(container);
    }

    const alertId = 'alert-' + Date.now();

    const alertHTML = `
        <div id="${alertId}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="relative w-[560px] rounded-[20px] bg-gradient-to-r from-slate-700 to-neutral-400 p-6 font-sans shadow-lg" style="background: linear-gradient(to right, #74aabf, #3986a3) top/100% 6px no-repeat, white; border-radius: 20px; background-clip: padding-box, border-box;">
                <div class="mx-auto h-[83px] w-[83px] flex items-center justify-center">
                    <i class="fas fa-spinner animate-spin text-blue-500" style="font-size: 48px;"></i>
                </div>

                <h2 class="mt-4 text-center font-sans text-2xl font-bold text-stone-900">
                    ${title}
                </h2>

                <p class="mt-2 text-center text-base font-medium text-black">
                    ${message}
                </p>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', alertHTML);

    const alertElement = document.getElementById(alertId);

    return {
        element: alertElement,
        close: () => {
            alertElement.style.display = 'none';
            alertElement.remove();
        }
    };
};

/**
 * Close alert yang sedang ditampilkan
 */
const closeAlert = () => {
    const container = document.getElementById('custom-alert-container');
    if (container) {
        container.innerHTML = '';
    }
};

/**
 * Utility untuk menampilkan form validation errors
 * @param {object} errors - Object errors dari server
 * @param {string} title - Judul alert (default: 'Validasi Gagal')
 */
const showValidationErrors = (errors, title = 'Validasi Gagal') => {
    let errorMessage = '';

    if (typeof errors === 'object') {
        Object.keys(errors).forEach((key) => {
            errorMessage += `${errors[key]}\n`;
        });
    } else {
        errorMessage = errors;
    }

    // Buat container jika belum ada
    let container = document.getElementById('custom-alert-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'custom-alert-container';
        document.body.appendChild(container);
    }

    const alertId = 'alert-' + Date.now();
    const icon = iconMap.error;

    const alertHTML = `
        <div id="${alertId}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="relative w-[560px] rounded-[20px] bg-gradient-to-r from-slate-700 to-neutral-400 p-6 font-sans shadow-lg max-h-96 overflow-y-auto" style="background: linear-gradient(to right, #74aabf, #3986a3) top/100% 6px no-repeat, white; border-radius: 20px; background-clip: padding-box, border-box;">
                <div class="mx-auto h-[83px] w-[83px] flex items-center justify-center">
                    ${icon}
                </div>

                <h2 class="mt-4 text-center font-sans text-2xl font-bold text-stone-900">
                    ${title}
                </h2>

                <div class="mt-4 text-center text-base font-medium text-black" style="text-align: left; word-break: break-word;">
                    ${errorMessage.replace(/\n/g, '<br>')}
                </div>

                <div class="mt-6 flex justify-center">
                    <button class="alert-close-btn rounded-[5px] bg-gradient-to-r from-[#74AABF] to-[#3986A3] px-10 py-2 font-medium text-white cursor-pointer">OK</button>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', alertHTML);

    const alertElement = document.getElementById(alertId);
    const closeBtn = alertElement.querySelector('.alert-close-btn');

    const close = () => {
        alertElement.style.display = 'none';
        alertElement.remove();
    };

    closeBtn.addEventListener('click', close);

    return {
        element: alertElement,
        close: close
    };
};

// Export untuk CommonJS jika digunakan dengan bundler
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        showSuccess,
        showError,
        showWarning,
        showInfo,
        showConfirm,
        showLoading,
        closeAlert,
        showValidationErrors,
    };
}
