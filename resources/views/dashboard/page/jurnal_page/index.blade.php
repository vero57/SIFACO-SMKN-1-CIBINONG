@extends(
    "dashboard.layout.app",
    [
        "title" => "Jurnal",
    ]
)

@section('content')
    <div class="content-section">
        <div class="backdrop-blur-sm rounded-xl px-6 mb-6 flex flex-row items-center justify-between">
            <div class="flex items-center gap-4">
                <i class="fas fa-book-open text-4xl text-purple-400"></i>
                <div>
                    <h3 class="text-lg md:text-2xl font-semibold text-black">Jurnal Siswa</h3>
                    <p class="text-slate-700 text-xs md:text-base">Daftar jurnal yang dikumpulkan siswa — nama, mata pelajaran, dan ringkasan deskripsi.</p>
                </div>
            </div>
            <div class="flex items-center">
                <a href="{{ route('dashboard.jurnal.exportExcel') }}" class="bg-purple-600 hover:bg-purple-500 text-white px-3 py-2 rounded text-sm"><i class="fa fa-file-download"></i> Ekspor Excel</a>
            </div>
        </div>

        <div class="backdrop-blur-sm rounded-xl p-2 md:p-4 border border-gray-300">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-2 md:mb-4 gap-2 md:gap-4">
                <div class="flex items-center gap-1 md:gap-2">
                    <button type="button" class="tab-btn px-2 md:px-3 py-1 rounded text-xs md:text-sm font-medium bg-slate-700 text-white border border-gray-700" data-view="student">Siswa</button>
                    <button type="button" class="tab-btn px-2 md:px-3 py-1 rounded text-xs md:text-sm font-medium text-slate-700 border border-gray-700" data-view="class">Kelas</button>
                </div>

                <div class="flex items-center gap-2 md:gap-3 w-full md:w-auto">
                    <input type="search" id="jurnal-search-input" placeholder="Cari nama siswa / pelajaran" class="flex-1 md:flex-none bg-white text-slate-700 border border-slate-700 rounded px-2 md:px-3 py-1 md:py-2 text-xs md:text-sm" />
                    <button type="button" id="jurnal-search-btn" class="bg-purple-600 hover:bg-purple-500 text-white px-2 md:px-3 py-1 md:py-2 rounded text-xs md:text-sm">Search</button>
                </div>
            </div>

            <div class="overflow-x-auto -mx-2 md:-mx-4">
                <table class="min-w-full table-auto border-collapse border border-gray-300">
                    <thead>
                        <tr class="text-left text-slate-700 text-sm uppercase tracking-wider bg-[#ecedf7]">
                            <th class="px-4 py-3 w-12">No</th>
                            <th class="px-4 py-3 col-name">Nama Siswa</th>
                            <th class="px-4 py-3">Pelajaran</th>
                            <th class="px-4 py-3">Deskripsi</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-300" id="jurnal-table-body">
                        @if(isset($journals) && $journals->count())
                            @foreach($journals as $idx => $j)
                                @php
                                    $no = $journals->firstItem() + $loop->index;
                                @endphp
                                <tr class="hover:bg-[#ecedf7] bg-white">
                                    <td class="px-4 py-3 text-slate-700 text-sm align-center">{{ $no }}</td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-purple-700 flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($j->student->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="view-student text-slate-700 font-medium text-sm">{{ $j->student->name ?? 'Nama Tidak Ditemukan' }}</div>
                                                <div class="view-class text-slate-700 font-medium text-sm hidden">{{ $j->student->classes->first()->name ?? '-' }}</div>
                                                <div class="text-slate-400 text-xs">ID: {{ $j->student_id }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 text-sm">
                                        <div class="font-medium">{{ $j->subject->name ?? 'Pelajaran Tidak Ditemukan' }}</div>
                                        <div class="text-slate-400 text-xs">{{ $j->created_at->format('Y-m-d H:i') }}</div>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 text-sm">
                                        <div class="text-sm text-slate-700 line-clamp-2" title="{{ $j->description }}">
                                            {{ $j->description }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 text-sm">
                                        <a href="{{ route('dashboard.jurnal.show', $j->id) }}" class="inline-block bg-blue-500 hover:bg-blue-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <!-- dummy data jika tidak ada data -->
                            <!-- @for($i=1;$i<=6;$i++)
                                <tr class="hover:bg-slate-800/40">
                                    <td class="px-4 py-3 text-slate-700 text-sm align-top">{{ $i }}</td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-purple-700 flex items-center justify-center text-white font-semibold">
                                                {{ chr(64 + $i) }}
                                            </div>
                                            <div>
                                                <div class="view-student text-slate-700 font-medium text-sm">Siswa {{ $i }}</div>
                                                <div class="view-class text-slate-700 font-medium text-sm hidden">Kelas {{ ceil($i/2) }}</div>
                                                <div class="text-slate-400 text-xs">ID: S00{{ $i }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 text-sm">
                                        <div class="font-medium">Matematika</div>
                                        <div class="text-slate-400 text-xs">{{ now()->subDays($i)->format('Y-m-d') }}</div>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 text-sm">
                                        <div class="text-sm text-slate-700 line-clamp-2" title="Deskripsi lengkap contoh jurnal">
                                            Contoh ringkasan jurnal untuk Siswa {{ $i }} tentang topik latihan dan hasil belajar pada pertemuan ini...
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 text-sm">
                                        <a href="" class="inline-block bg-blue-500 hover:bg-blue-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endfor -->

                            <tr class="hover:bg-slate-800/40">
                                <td colspan="5" class="px-4 py-3 text-slate-700 text-sm text-center">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-2 md:mt-4 flex flex-col md:flex-row items-center justify-between text-slate-400 text-xs md:text-sm gap-2">
                <div>Showing <span class="text-slate-700">{{ $journals->firstItem() ?? 0 }}</span> to <span class="text-slate-700">{{ $journals->lastItem() ?? 0 }}</span> entries</div>
                <div class="text-xs md:text-sm">
                    {{ $journals->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // AJAX Search untuk Jurnal - tanpa page reload
    const searchInput = document.querySelector('#jurnal-search-input');
    const searchBtn = document.querySelector('#jurnal-search-btn');
    const tableBody = document.querySelector('#jurnal-table-body');
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

        fetch(`{{ route("dashboard.jurnal") }}?${params.toString()}`, {
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
            tableBody.innerHTML = `<tr><td colspan="5" class="px-4 py-3 text-center text-slate-400">Data tidak ditemukan</td></tr>`;
            return;
        }

        data.data.forEach((journal, index) => {
            const student = journal.student;
            const subject = journal.subject;
            const initial = student?.name ? student.name.charAt(0).toUpperCase() : 'U';
            const createdAt = new Date(journal.created_at).toLocaleDateString('id-ID');

            const row = `
                <tr class="hover:bg-slate-800/40">
                    <td class="px-4 py-3 text-slate-700 text-sm align-center">${index + 1}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-700 flex items-center justify-center text-white font-semibold">
                                ${initial}
                            </div>
                            <div>
                                <div class="view-student text-slate-700 font-medium text-sm">${student?.name || 'Nama Tidak Ditemukan'}</div>
                                <div class="view-class text-slate-700 font-medium text-sm hidden">${student?.classes?.[0]?.name || '-'}</div>
                                <div class="text-slate-400 text-xs">ID: ${journal.student_id}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-slate-700 text-sm">
                        <div class="font-medium">${subject?.name || 'Pelajaran Tidak Ditemukan'}</div>
                        <div class="text-slate-400 text-xs">${createdAt}</div>
                    </td>
                    <td class="px-4 py-3 text-slate-700 text-sm">
                        <div class="text-sm text-slate-700 line-clamp-2" title="${journal.description}">
                            ${journal.description}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-slate-700 text-sm">
                        <a href="/dashboard/jurnal/${journal.id}" class="inline-block bg-blue-500 hover:bg-blue-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
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
                    b.classList.remove('text-slate-700');
                } else {
                    b.classList.remove('bg-slate-700','text-white');
                    b.classList.add('text-slate-700');
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
