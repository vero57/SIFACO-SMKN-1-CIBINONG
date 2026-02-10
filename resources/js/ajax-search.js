/**
 * AJAX Search Helper - Digunakan untuk semua search bar di Dashboard
 * Menangani pencarian tanpa reload halaman
 */

class AjaxSearch {
    constructor(options = {}) {
        this.searchInput = options.searchInput; // Selector untuk input search
        this.searchButton = options.searchButton; // Selector untuk tombol search
        this.tableBody = options.tableBody; // Selector untuk tbody table
        this.apiUrl = options.apiUrl; // URL endpoint API
        this.debounceDelay = options.debounceDelay || 300;
        this.additionalFilters = options.additionalFilters || {}; // Filter tambahan (select, date, dll)
        this.renderRow = options.renderRow; // Function untuk render setiap row
        this.loadingElement = options.loadingElement || null; // Element loading indicator
        this.resultContainer = options.resultContainer; // Selector untuk container hasil (optional)

        this.debounceTimer = null;

        this.init();
    }

    init() {
        // Event listener untuk search input
        if (this.searchInput) {
            const input = document.querySelector(this.searchInput);
            if (input) {
                input.addEventListener('keyup', (e) => {
                    this.handleSearch(e);
                });
            }
        }

        // Event listener untuk search button
        if (this.searchButton) {
            const btn = document.querySelector(this.searchButton);
            if (btn) {
                btn.addEventListener('click', () => {
                    this.performSearch();
                });
            }
        }

        // Event listener untuk filter tambahan
        this.setupAdditionalFilters();
    }

    setupAdditionalFilters() {
        if (this.additionalFilters.selectors && Array.isArray(this.additionalFilters.selectors)) {
            this.additionalFilters.selectors.forEach(selector => {
                const element = document.querySelector(selector);
                if (element) {
                    element.addEventListener('change', () => {
                        this.performSearch();
                    });
                }
            });
        }
    }

    handleSearch(event) {
        // Handle Enter key
        if (event.key === 'Enter') {
            this.performSearch();
            return;
        }

        // Debounce untuk setiap karakter
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.performSearch();
        }, this.debounceDelay);
    }

    performSearch() {
        const searchValue = document.querySelector(this.searchInput)?.value || '';

        // Kumpulkan semua filter
        const params = new URLSearchParams();
        params.append('search', searchValue);

        // Tambahkan filter tambahan
        if (this.additionalFilters.selectors) {
            this.additionalFilters.selectors.forEach(selector => {
                const element = document.querySelector(selector);
                if (element && element.value) {
                    const paramName = element.getAttribute('name') || element.id;
                    params.append(paramName, element.value);
                }
            });
        }

        this.showLoading();

        fetch(`${this.apiUrl}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            this.renderResults(data);
            this.hideLoading();
        })
        .catch(error => {
            console.error('Search error:', error);
            this.hideLoading();
            this.showError('Terjadi kesalahan saat mencari data');
        });
    }

    renderResults(data) {
        const tbody = document.querySelector(this.tableBody);

        if (!tbody) {
            console.error('Table body element not found');
            return;
        }

        // Kosongkan tbody
        tbody.innerHTML = '';

        // Jika tidak ada data
        if (!data.data || data.data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="100%" class="px-4 py-3 text-center text-slate-400">Data tidak ditemukan</td></tr>`;
            return;
        }

        // Render setiap row
        data.data.forEach((item, index) => {
            const row = this.renderRow(item, index);
            tbody.insertAdjacentHTML('beforeend', row);
        });

        // Update info jika ada
        if (data.info && this.resultContainer) {
            const container = document.querySelector(this.resultContainer);
            if (container) {
                container.textContent = data.info;
            }
        }
    }

    showLoading() {
        if (this.loadingElement) {
            const element = document.querySelector(this.loadingElement);
            if (element) {
                element.style.display = 'block';
            }
        }
    }

    hideLoading() {
        if (this.loadingElement) {
            const element = document.querySelector(this.loadingElement);
            if (element) {
                element.style.display = 'none';
            }
        }
    }

    showError(message) {
        // Bisa menggunakan SweetAlert jika tersedia
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }
}

// Export untuk digunakan di modul
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AjaxSearch;
}
