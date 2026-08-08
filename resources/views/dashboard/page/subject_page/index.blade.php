@extends(
    "dashboard.layout.app",
    [
        "title" => "Mata Pelajaran",
    ]
)

@section('content')
<div class="content-section">
    <div class="bg-slate-800/50 flex flex-row items-center justify-between backdrop-blur-sm rounded-xl p-6 border border-slate-700">
        <div class="flex items-center gap-4">
            <i class="fas fa-book text-4xl text-green-400"></i>
            <div>
                <h3 class="text-2xl font-semibold text-white">Mata Pelajaran</h3>
                <p class="text-slate-400">Daftar Mata Pelajaran.</p>
            </div>
        </div>
        @php
            $role = auth()->check() && auth()->user()->role ? auth()->user()->role->name : null;
        @endphp
        @if($role === 'Admin')
        <div class="flex items-center">
            <a href="{{ route('dashboard.subjects.create') }}"><button type="button" class="bg-green-600 hover:bg-green-500 text-white px-3 py-2 rounded text-sm">Tambah Mata Pelajaran</button></a>
        </div>
        @endif
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
            <form class="flex items-center gap-3" id="subject-search-form">
                <input type="search" id="subject-search-input" name="search" placeholder="Cari nama kelas, mapel, atau guru" value="{{ request('search', $search ?? '') }}" class="bg-slate-900 text-slate-200 border border-slate-700 rounded px-3 py-2 text-sm" />
                <button type="button" id="subject-search-btn" class="bg-green-600 hover:bg-green-500 text-white px-3 py-2 rounded text-sm">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto -mx-4 px-4">
            <table class="min-w-full table-auto border-collapse">
                <thead>
                    <tr class="text-left text-slate-300 text-sm uppercase tracking-wider">
                        <th class="px-4 py-3">Nama Mata Pelajaran</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Guru</th>
                        <th class="px-4 py-3 w-[200px]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700" id="subject-table-body">
                @if(isset($subjects) && $subjects->count())
                    @foreach($subjects as $classSubject)
                        <tr class="hover:bg-slate-800/40">
                            <td class="px-4 py-3 text-slate-200 text-sm">{{ $classSubject->subject->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-200 text-sm">{{ $classSubject->class->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-200 text-sm">{{ $classSubject->teacher->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-200 text-sm">
                                <a href="{{ route('dashboard.subjects.edit', $classSubject->id) }}" class="inline-block bg-yellow-500 hover:bg-yellow-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @if($role === 'Admin')
                                <form id="delete-form-{{ $classSubject->id }}" action="{{ route('dashboard.subjects.destroy', $classSubject->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('{{ $classSubject->id }}', '{{ $classSubject->subject->name ?? 'Mata Pelajaran' }}')" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded text-xs font-semibold">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center justify-between text-slate-400 text-sm">
            <div>Showing <span class="text-white">{{ $subjects->firstItem() }}</span> to <span class="text-white">{{ $subjects->lastItem() }}</span> of <span class="text-white">{{ $subjects->total() }}</span> entries</div>
            @if(isset($subjects) && method_exists($subjects, 'links'))
                <div class="text-sm">
                    {{ $subjects->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // AJAX Search untuk Mata Pelajaran - tanpa page reload
    const searchInput = document.querySelector('#subject-search-input');
    const searchBtn = document.querySelector('#subject-search-btn');
    const tableBody = document.querySelector('#subject-table-body');
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

        fetch(`{{ route("dashboard.subjects.index") }}?${params.toString()}`, {
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
            tableBody.innerHTML = `<tr><td colspan="4" class="px-4 py-3 text-center text-slate-400">Data tidak ditemukan</td></tr>`;
            return;
        }

        const role = '{{ $role }}';

        data.data.forEach((classSubject) => {
            let actionHtml = `
                <a href="/dashboard/subjects/${classSubject.id}/edit" class="inline-block bg-yellow-500 hover:bg-yellow-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
            `;

            if (role === 'Admin') {
                actionHtml += `
                    <button type="button" onclick="confirmDelete('${classSubject.id}', '${classSubject.subject?.name || 'Mata Pelajaran'}')" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded text-xs font-semibold">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                `;
            }

            const row = `
                <tr class="hover:bg-slate-800/40">
                    <td class="px-4 py-3 text-slate-200 text-sm">${classSubject.subject?.name || '-'}</td>
                    <td class="px-4 py-3 text-slate-200 text-sm">${classSubject.class?.name || '-'}</td>
                    <td class="px-4 py-3 text-slate-200 text-sm">${classSubject.teacher?.name || '-'}</td>
                    <td class="px-4 py-3 text-slate-200 text-sm">${actionHtml}</td>
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

    function confirmDelete(id, subjectName) {
        showConfirm(
            `Mata pelajaran "${subjectName}" akan dihapus secara permanen.`,
            'Yakin ingin menghapus?',
            function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/dashboard/subjects/${id}`;
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
