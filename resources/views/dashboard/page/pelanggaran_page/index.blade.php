@extends(
    "dashboard.layout.app",
    [
        "title" => "Pelanggaran Siswa",
    ]
)

@section('content')
    <div class="content-section">
        <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700 mb-6 flex flex-row items-center justify-between">
            <div class="flex items-center gap-4">
                <i class="fas fa-exclamation-triangle text-4xl text-red-400"></i>
                <div>
                    <h3 class="text-2xl font-semibold text-white">Pelanggaran Siswa</h3>
                    <p class="text-slate-400">Daftar pelanggaran siswa — tanggal, jam, status, dan lokasi.</p>
                </div>
            </div>
            <div class="flex items-center">
                <a href="{{ route('dashboard.pelanggaran.exportPdf') }}" class="bg-red-600 hover:bg-red-500 text-white px-3 py-2 rounded text-sm">Ekspor Data</a>
            </div>
        </div>


        <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
                <div class="flex items-center gap-2">
                    <!-- <button type="button" class="tab-btn px-3 py-1 rounded text-sm font-medium bg-slate-700 text-white" data-view="student">Siswa</button> -->
                    <!-- <button type="button" class="tab-btn px-3 py-1 rounded text-sm font-medium text-slate-300 hover:text-white" data-view="class">Kelas</button> -->
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <input type="search" id="pelanggaran-search-input" placeholder="Cari nama siswa / pelajaran" class="flex-1 md:flex-none bg-slate-900 text-slate-200 border border-slate-700 rounded px-3 py-2 text-sm" />
                    <button type="button" id="pelanggaran-search-btn" class="bg-red-600 hover:bg-red-500 text-white px-3 py-2 rounded text-sm">Search</button>
                </div>
            </div>

            <div class="overflow-x-auto -mx-4 px-4">
                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr class="text-left text-slate-300 text-sm uppercase tracking-wider">
                            <th class="px-4 py-3 w-12">No</th>
                            <th class="px-4 py-3 col-name w-64">Nama Siswa</th>
                            <th class="px-4 py-3 w-32">Kelas</th>
                            <th class="px-4 py-3 w-52">Pelanggaran</th>
                            <th class="px-4 py-3 w-32">Tanggal</th>
                            <th class="px-4 py-3 w-32">Poin</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-700" id="pelanggaran-table-body">
                        @if(isset($violations) && $violations->count())
                            @foreach($violations as $idx => $v)
                                <tr class="hover:bg-slate-800/40">
                                    <td class="px-4 py-3 text-slate-200 text-sm align-top">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-purple-700 flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($v->student->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="view-student text-slate-200 font-medium text-sm">{{ $v->student->name ?? '-' }}</div>
                                                <div class="view-class text-slate-200 font-medium text-sm hidden">{{ $v->student->classes->first()->name ?? '-' }}</div>
                                                <div class="text-slate-400 text-xs">{{ $v->student_id ? 'ID: '.$v->student_id : '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-200 text-sm">
                                        <div class="font-medium">{{ $v->student->classes->first()->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-200 text-sm">
                                        <div class="text-sm text-slate-200">{{ $v->rule->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-200 text-sm">
                                        {{ $v->created_at ? $v->created_at->format('Y-m-d') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-200 text-sm">
                                        {{ $v->rule->points ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-200 text-sm">
                                        <a href="{{ route('dashboard.pelanggaran.show', $v->id) }}" class="inline-block bg-blue-500 hover:bg-blue-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center text-slate-400 py-6">Tidak ada data pelanggaran.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center justify-between text-slate-400 text-sm">
                <div>Showing <span class="text-white">1</span> to <span class="text-white">10</span> entries</div>
                @if(isset($violations) && method_exists($violations, 'links'))
                    <div class="text-sm">
                        {{ $violations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // AJAX Search untuk Pelanggaran - tanpa page reload
    const searchInput = document.querySelector('#pelanggaran-search-input');
    const searchBtn = document.querySelector('#pelanggaran-search-btn');
    const tableBody = document.querySelector('#pelanggaran-table-body');
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

    function performSearch() {
        const searchValue = searchInput.value || '';
        const params = new URLSearchParams();
        params.append('search', searchValue);

        fetch(`{{ route("dashboard.pelanggaran") }}?${params.toString()}`, {
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
            tableBody.innerHTML = `<tr><td colspan="7" class="px-4 py-3 text-center text-slate-400">Data tidak ditemukan</td></tr>`;
            return;
        }

        data.data.forEach((violation, index) => {
            const student = violation.student;
            const initial = student?.name ? student.name.charAt(0).toUpperCase() : 'U';
            const createdAt = violation.created_at ? new Date(violation.created_at).toLocaleDateString('id-ID') : '-';
            const className = student?.classes?.[0]?.name || '-';
            const ruleName = violation.rule?.name || '-';
            const points = violation.rule?.points || '-';

            const row = `
                <tr class="hover:bg-slate-800/40">
                    <td class="px-4 py-3 text-slate-200 text-sm align-top">${index + 1}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-700 flex items-center justify-center text-white font-semibold">
                                ${initial}
                            </div>
                            <div>
                                <div class="view-student text-slate-200 font-medium text-sm">${student?.name || '-'}</div>
                                <div class="view-class text-slate-200 font-medium text-sm hidden">${className}</div>
                                <div class="text-slate-400 text-xs">${violation.student_id ? 'ID: ' + violation.student_id : ''}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-slate-200 text-sm">
                        <div class="font-medium">${className}</div>
                    </td>
                    <td class="px-4 py-3 text-slate-200 text-sm">
                        <div class="text-sm text-slate-200">${ruleName}</div>
                    </td>
                    <td class="px-4 py-3 text-slate-200 text-sm">${createdAt}</td>
                    <td class="px-4 py-3 text-slate-200 text-sm">${points}</td>
                    <td class="px-4 py-3 text-slate-200 text-sm">
                        <a href="/dashboard/pelanggaran/${violation.id}" class="inline-block bg-blue-500 hover:bg-blue-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
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

    // Tab switching functionality
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const colName = document.querySelector('.col-name');

        const showView = (view) => {
            tabButtons.forEach(b => {
                if (b.dataset.view === view) {
                    b.classList.add('bg-slate-700','text-white');
                    b.classList.remove('text-slate-300', 'hover:text-white');
                } else {
                    b.classList.remove('bg-slate-700','text-white');
                    b.classList.add('text-slate-300', 'hover:text-white');
                }
            });

            if (colName) colName.textContent = (view === 'student') ? 'Nama Siswa' : 'Kelas';

            document.querySelectorAll('.view-student').forEach(el => el.classList.toggle('hidden', view !== 'student'));
            document.querySelectorAll('.view-class').forEach(el => el.classList.toggle('hidden', view !== 'class'));
        };

        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => showView(btn.dataset.view));
        });

        showView('student');
    });
</script>
@endpush
