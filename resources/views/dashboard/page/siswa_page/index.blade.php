@extends(
    "dashboard.layout.app",
    [
        "title" => "Siswa",
    ]
)

@section('content')
<div class="content-section">
    <div class="bg-slate-800/50 flex flex-row items-center justify-between backdrop-blur-sm rounded-xl p-6 border border-slate-700">
        <div class="flex items-center gap-4">
            <i class="fas fa-user-graduate text-4xl text-blue-400"></i>
            <div>
                <h3 class="text-2xl font-semibold text-white">Siswa</h3>
                <p class="text-slate-400">Daftar Siswa.</p>
            </div>
        </div>
        <div class="flex items-center">
            <a href="{{ route('dashboard.users.create') }}">
                <button type="button" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-2 rounded text-sm">Tambah Data Siswa</button>
            </a>
            <form action="{{ route('dashboard.siswa.import') }}" method="POST" enctype="multipart/form-data" class="ml-2">
                @csrf
                <label class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-2 rounded text-sm cursor-pointer mb-0">
                    Import Data Siswa
                    <input type="file" name="file" accept=".xlsx,.xls" class="hidden" onchange="this.form.submit()">
                </label>
            </form>
        </div>
    </div>

    <div class="mt-6 bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
            <div class="flex items-center gap-3">
                <label class="text-slate-300 text-sm">Show</label>
                <select class="bg-slate-900 text-slate-200 border border-slate-700 rounded px-2 py-1 text-sm">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
            </div>

            <form class="flex items-center gap-3" id="siswa-search-form">
                <input type="search" id="siswa-search-input" name="search" placeholder="Cari nama/NIS/NISN" value="{{ request('search', $search ?? '') }}" class="bg-slate-900 text-slate-200 border border-slate-700 rounded px-3 py-2 text-sm" />
                <button type="button" id="siswa-search-btn" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-2 rounded text-sm">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto -mx-4 px-4">
            <table class="min-w-full table-auto border-collapse">
                <thead>
                    <tr class="text-left text-slate-300 text-sm uppercase tracking-wider">
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Gender</th>
                        <th class="px-4 py-3">NIS</th>
                        <!-- <th class="px-4 py-3">Tempat Lahir</th> -->
                        <!-- <th class="px-4 py-3">Tanggal Lahir</th> -->
                        <!-- <th class="px-4 py-3">Alamat</th> -->
                        <th class="px-4 py-3">Foto</th>
                        <th class="px-4 py-3 min-w-[300px] w-[300px]">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-700" id="siswa-table-body">
                @if(isset($users) && $users->count())
                    @foreach($users as $user)
                        @php
                            $detail = $user->studentDetail;
                        @endphp
                            <tr class="hover:bg-slate-800/40">
                                <td class="px-4 py-3 text-slate-200 text-sm">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-slate-200 text-sm">{{ $user->email ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-200 text-sm">{{ $detail->gender ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-200 text-sm">{{ $detail->nis ?? '-' }}</td>
                                <!-- <td class="px-4 py-3 text-slate-200 text-sm">{{ $detail->birth_place ?? '-' }}</td> -->
                                <!-- <td class="px-4 py-3 text-slate-200 text-sm">
                                    {{ ($detail && $detail->birth_date) ? \Carbon\Carbon::parse($detail->birth_date)->format('d-m-Y') : '-' }}
                                </td> -->
                                <!-- <td class="px-4 py-3 text-slate-200 text-sm">{{ $detail->address ?? '-' }}</td> -->
                                <td class="px-4 py-3 text-slate-200 text-sm">
                                    @if($detail && $detail->photo)
                                        <img src="{{ asset('storage/' . $detail->photo) }}" alt="Foto" class="w-12 h-12 object-cover rounded" />
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-200 text-sm flex flex-row items-center gap-1">
                                    @php $hasDetail = $detail !== null; @endphp
                                    <a href="{{ $hasDetail ? route('dashboard.siswa.detail.edit', $user->id) : route('dashboard.siswa.detail.create', $user->id) }}" class="w-full flex flex-row gap-1 justify-center items-center bg-yellow-500 hover:bg-yellow-400 text-white px-1 py-1 rounded text-xs font-semibold mr-2">
                                        <i class="fas fa-edit"></i> <p class="">{{ $hasDetail ? 'Edit Detail' : 'Add Detail' }}</p>
                                    </a>
                                    <a href="{{ route('dashboard.siswa.detail.show', $user->id) }}" class="flex flex-row gap-1 items-center bg-blue-500 hover:bg-blue-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <form id="delete-form-{{ $user->id }}" action="{{ route('dashboard.users.destroy', $user->id) }}" method="POST" class="flex flex-row gap-1 items-center">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')" class="flex flex-row gap-1 items-center bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded text-xs font-semibold">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center justify-between text-slate-400 text-sm">
            <div>Showing <span class="text-white">{{ $users->firstItem() }}</span> to <span class="text-white">{{ $users->lastItem() }}</span> of <span class="text-white">{{ $users->total() }}</span> entries</div>
            @if(isset($users) && method_exists($users, 'links'))
                <div class="text-sm">
                    {{ $users->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // AJAX Search untuk Siswa - tanpa page reload
    const searchInput = document.querySelector('#siswa-search-input');
    const searchBtn = document.querySelector('#siswa-search-btn');
    const tableBody = document.querySelector('#siswa-table-body');
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

        fetch(`{{ route("dashboard.siswa") }}?${params.toString()}`, {
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
                displayError('Terjadi kesalahan saat mencari data');
            });
    }

    function renderResults(data) {
        if (!tableBody) return;

        tableBody.innerHTML = '';

        if (!data.data || data.data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" class="px-4 py-3 text-center text-slate-400">Data tidak ditemukan</td></tr>`;
            return;
        }

        data.data.forEach((user) => {
            const detail = user.student_detail;
            const photoHtml = detail && detail.photo
                ? `<img src="/storage/${detail.photo}" alt="Foto" class="w-12 h-12 object-cover rounded" />`
                : `<span class="text-slate-400">-</span>`;

            const hasDetail = detail !== null;
            const detailUrl = hasDetail
                ? `/dashboard/siswa/${user.id}/detail/edit`
                : `/dashboard/siswa/${user.id}/detail/create`;

            const row = `
                <tr class="hover:bg-slate-800/40">
                    <td class="px-4 py-3 text-slate-200 text-sm">${user.name}</td>
                    <td class="px-4 py-3 text-slate-200 text-sm">${user.email || '-'}</td>
                    <td class="px-4 py-3 text-slate-200 text-sm">${(detail && detail.gender) ? detail.gender : '-'}</td>
                    <td class="px-4 py-3 text-slate-200 text-sm">${(detail && detail.nis) ? detail.nis : '-'}</td>
                    <td class="px-4 py-3 text-slate-200 text-sm">${photoHtml}</td>
                    <td class="px-4 py-3 text-slate-200 text-sm flex flex-row items-center gap-1">
                        <a href="${detailUrl}" class="w-full flex flex-row gap-1 justify-center items-center bg-yellow-500 hover:bg-yellow-400 text-white px-1 py-1 rounded text-xs font-semibold mr-2">
                            <i class="fas fa-edit"></i> <p class="">${hasDetail ? 'Edit Detail' : 'Add Detail'}</p>
                        </a>
                        <a href="/dashboard/siswa/${user.id}/detail" class="flex flex-row gap-1 items-center bg-blue-500 hover:bg-blue-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        <button type="button" onclick="confirmDelete('${user.id}', '${user.name}')" class="flex flex-row gap-1 items-center bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded text-xs font-semibold">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    }

    function displayError(message) {
        showError(message, 'Error!');
    }

    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            showSuccess('{{ session("success") }}', 'Berhasil!');
        });
    @endif

    function confirmDelete(userId, userName) {
        showConfirm(
            `Siswa "${userName}" akan dihapus secara permanen.`,
            'Yakin ingin menghapus?',
            function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/dashboard/users/${userId}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        );
    }
</script>
@endpush
