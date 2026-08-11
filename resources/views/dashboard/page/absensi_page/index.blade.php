@extends(
    "dashboard.layout.app",
    [
        "title" => "Presensi Siswa",
    ]
)

@section('content')
<div class="content-section">
    <div class="flex flex-row items-center justify-between backdrop-blur-sm rounded-xl px-6">
        <div class="flex items-center gap-4">
            <i class="fas fa-users text-4xl text-green-400"></i>
            <div>
                <h3 class="text-2xl font-semibold text-black">Presensi Siswa</h3>
                <p class="text-slate-700">Daftar rekaman kehadiran siswa — tanggal, jam, status, dan lokasi.</p>
            </div>
        </div>
        <div class="flex items-center">
            <a href="{{ route('dashboard.absensi.exportExcel') }}" class="bg-green-600 hover:bg-green-500 text-white px-3 py-2 rounded text-sm"><i class="fa fa-file-download"></i> Ekspor Excel</a>
        </div>
    </div>

    <div class="mt-6 backdrop-blur-sm rounded-xl p-4 border border-gray-300">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
            <div class="flex items-center gap-3">
                <label class="text-slate-700 text-sm">Show</label>
                <select class="bg-white text-slate-700 border border-gray-300 rounded px-2 py-1 text-sm">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
            </div>

            <form class="flex flex-wrap items-center gap-3" id="absensi-search-form">
                <input type="search" id="absensi-search-input" name="search" placeholder="Cari nama/NIS/NISN/kelas" value="{{ request('search', $search ?? '') }}" class="bg-white text-slate-700 border border-gray-300 rounded px-3 py-2 text-sm" />

                <select name="kelas" id="absensi-kelas-filter" class="bg-white text-slate-700 border border-gray-300 rounded px-2 py-2 text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ (request('kelas', $kelas_id ?? '') == $kelas->id) ? 'selected' : '' }}>{{ $kelas->name }}</option>
                    @endforeach
                </select>

                <select name="status" id="absensi-status-filter" class="bg-white text-slate-700 border border-gray-300 rounded px-2 py-2 text-sm">
                    <option value="">Semua Status</option>
                    @foreach($statusList as $status)
                        <option value="{{ $status->id }}" {{ (request('status', $status_id ?? '') == $status->id) ? 'selected' : '' }}>{{ $status->name }}</option>
                    @endforeach
                </select>

                <input type="date" id="absensi-tanggal-filter" name="tanggal" value="{{ request('tanggal', $tanggal ?? '') }}" class="bg-white text-slate-700 border border-gray-300 rounded px-2 py-2 text-sm" />

                <button type="button" id="absensi-search-btn" class="bg-green-600 hover:bg-green-500 text-white px-3 py-2 rounded text-sm">Filter</button>
                <a href="{{ route('dashboard.absensi') }}" class="bg-slate-700 hover:bg-slate-600 text-white px-3 py-2 rounded text-sm">Reset</a>
            </form>
        </div>

        <div class="overflow-x-auto -mx-4">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead>
                    <tr class="text-left text-slate-700 text-sm uppercase tracking-wider bg-[#ecedf7]">
                        <th class="px-4 py-3">Nama Siswa</th>
                        <th class="px-4 py-3">Kelas Siswa</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Waktu Masuk</th>
                        <th class="px-4 py-3">Waktu Keluar</th>
                        <th class="px-4 py-3">Status Kehadiran</th>
                        <th class="px-4 py-3">Foto</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300" id="absensi-table-body">
                    @if(isset($attendances) && $attendances->count())
                        @foreach($attendances as $attendance)
                            <tr class="hover:bg-[#ecedf7] bg-white">
                                <td class="px-4 py-3 text-slate-700 text-sm">
                                    {{ $attendance->student->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-700 text-sm">
                                    {{ $attendance->student && $attendance->student->classes->count() ? $attendance->student->classes->pluck('name')->join(', ') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-700 text-sm">{{ $attendance->date }}</td>
                                <td class="px-4 py-3 text-slate-700 text-sm">{{ $attendance->time_in ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-700 text-sm">{{ $attendance->time_out ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-3 py-1 text-xs font-medium rounded bg-slate-300">
                                        {{ $attendance->status->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-700 text-sm">
                                    @if($attendance->photo)
                                        <img src="{{ asset($attendance->photo) }}" alt="photo" class="w-12 h-12 rounded-md object-cover border border-gray-300 cursor-pointer preview-photo" data-photo="{{ asset($attendance->photo) }}">
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700 text-sm">
                                    <a href="{{ route('dashboard.absensi.show', $attendance->id) }}" class="inline-block bg-blue-500 hover:bg-blue-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center text-slate-400 py-6">Tidak ada data presensi.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center justify-between text-slate-500 text-sm">
            <div>
                Showing <span class="text-slate-700">{{ $attendances->firstItem() }}</span> to <span class="text-slate-700">{{ $attendances->lastItem() }}</span> of <span class="text-slate-700">{{ $attendances->total() }}</span> entries
            </div>
            @if(isset($attendances) && method_exists($attendances, 'links'))
                <div class="text-sm">
                    {{ $attendances->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Preview Gambar -->
<div id="photoModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100vw; height:100vh; background:rgba(30,41,59,0.85); align-items:center; justify-content:center;">
    <div style="position:relative; max-width:90vw; max-height:90vh; display:flex; align-items:center; justify-content:center;">
        <img id="modalImg" src="" alt="Preview" style="max-width:90vw; max-height:80vh; border-radius:1rem; box-shadow:0 8px 32px rgba(0,0,0,0.25); background:#222;">
        <button id="closeModalBtn" type="button" style="position:absolute; top:-18px; right:-18px; background:#f87171; color:white; border:none; border-radius:50%; width:36px; height:36px; font-size:1.5rem; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.18);">×</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // AJAX Search untuk Presensi - tanpa page reload
    const searchInput = document.querySelector('#absensi-search-input');
    const searchBtn = document.querySelector('#absensi-search-btn');
    const tableBody = document.querySelector('#absensi-table-body');
    const kelasFilter = document.querySelector('#absensi-kelas-filter');
    const statusFilter = document.querySelector('#absensi-status-filter');
    const tanggalFilter = document.querySelector('#absensi-tanggal-filter');
    let debounceTimer = null;

    if (searchBtn) {
        searchBtn.addEventListener('click', (e) => {
            e.preventDefault();
            performSearch();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                performSearch();
                return;
            }
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                performSearch();
            }, 300);
        });
    }

    // Handle filter changes
    [kelasFilter, statusFilter, tanggalFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('change', () => {
                performSearch();
            });
        }
    });

    function performSearch() {
        const searchValue = searchInput.value || '';
        const params = new URLSearchParams();
        params.append('search', searchValue);

        if (kelasFilter && kelasFilter.value) {
            params.append('kelas', kelasFilter.value);
        }
        if (statusFilter && statusFilter.value) {
            params.append('status', statusFilter.value);
        }
        if (tanggalFilter && tanggalFilter.value) {
            params.append('tanggal', tanggalFilter.value);
        }

        fetch(`{{ route("dashboard.absensi") }}?${params.toString()}`, {
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
                renderResults(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                showError('Terjadi kesalahan saat mencari data');
            });
    }

    function renderResults(data) {
        if (!tableBody) return;

        tableBody.innerHTML = '';

        if (!data.data || data.data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="8" class="px-4 py-3 text-center text-slate-400">Data tidak ditemukan</td></tr>`;
            return;
        }

        data.data.forEach((attendance) => {
            const student = attendance.student;
            const className = (student.classes && student.classes.length > 0)
                ? student.classes.map(c => c.name).join(', ')
                : '-';

            const photoHtml = attendance.photo
                ? `<img src="${attendance.photo}" alt="photo" class="w-12 h-12 rounded-md object-cover border border-gray-300 cursor-pointer preview-photo" data-photo="${attendance.photo}">`
                : `<span class="text-slate-400">-</span>`;

            const row = `
                <tr class="hover:bg-slate-800/40">
                    <td class="px-4 py-3 text-slate-700 text-sm">${student.name || '-'}</td>
                    <td class="px-4 py-3 text-slate-700 text-sm">${className}</td>
                    <td class="px-4 py-3 text-slate-700 text-sm">${attendance.date || '-'}</td>
                    <td class="px-4 py-3 text-slate-700 text-sm">${attendance.time_in || '-'}</td>
                    <td class="px-4 py-3 text-slate-700 text-sm">${attendance.time_out || '-'}</td>
                    <td class="px-4 py-3">
                        <span class="inline-block px-3 py-1 text-xs font-medium rounded bg-slate-600">
                            ${attendance.status?.name || '-'}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-700 text-sm">${photoHtml}</td>
                    <td class="px-4 py-3 text-slate-700 text-sm">
                        <a href="/dashboard/absensi/${attendance.id}" class="inline-block bg-blue-500 hover:bg-blue-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });

        // Reattach photo click listeners
        attachPhotoClickListeners();
    }

    function showError(message) {
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

    function attachPhotoClickListeners() {
        document.querySelectorAll('.preview-photo').forEach(function(img) {
            img.removeEventListener('click', handlePhotoClick);
            img.addEventListener('click', handlePhotoClick);
        });
    }

    function handlePhotoClick(e) {
        e.stopPropagation();
        const modal = document.getElementById('photoModal');
        const modalImg = document.getElementById('modalImg');
        modalImg.src = this.dataset.photo;
        modal.style.display = 'flex';
    }

    // Close modal
    document.getElementById('closeModalBtn').onclick = function(e) {
        e.stopPropagation();
        document.getElementById('photoModal').style.display = 'none';
        document.getElementById('modalImg').src = '';
    };

    // Close modal on outside click
    document.getElementById('photoModal').onclick = function(e) {
        if (e.target === this) {
            this.style.display = 'none';
            document.getElementById('modalImg').src = '';
        }
    };

    // Initial attachment
    attachPhotoClickListeners();
</script>
@endpush

