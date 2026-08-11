@extends(
    "dashboard.layout.app",
    [
        "title" => "Users",
    ]
)

@section('content')
<div class="content-section">
    <div class="flex flex-row items-center justify-between backdrop-blur-sm rounded-xl px-6">
        <div class="flex items-center gap-4">
            <i class="fas fa-users text-4xl text-green-400"></i>
            <div>
                <h3 class="text-2xl font-semibold text-black">Manajemen User</h3>
                <p class="text-slate-700">Daftar Semua Akun User.</p>
            </div>
        </div>
        <div class="flex items-center">
            <a href="{{ route('dashboard.users.create') }}"><button type="button" class="bg-green-600 hover:bg-green-500 text-white px-3 py-2 rounded text-sm"><i class="fa fa-plus"></i> Tambah Data User</button></a>
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

            <form class="flex items-center gap-3" id="user-search-form">
                <input type="search" id="user-search-input" name="search" value="{{ request('search') }}" placeholder="Cari nama/email" class="bg-white placeholder:text-slate-600 text-slate-700 border border-gray-300 rounded px-3 py-2 text-sm" />
                <button type="button" id="user-search-btn" class="bg-green-600 hover:bg-green-500 text-white px-3 py-2 rounded text-sm">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto -mx-4">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead>
                    <tr class="text-left bg-[#ecedf7] text-slate-700 text-sm uppercase tracking-wider border border-gray-300">
                        <th class="px-4 py-3">Nama User</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">No HP</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-300" id="user-table-body">
                    @if(isset($users) && $users->count())
                        @foreach($users as $user)
                            <tr class="hover:bg-[#ecedf7] bg-white">
                                <td class="px-4 py-3 text-slate-700 text-sm">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-slate-700 text-sm">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-slate-700 text-sm">{{ $user->phone_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-700 text-sm">
                                    @if($user->role)
                                        <span class="inline-block px-2 py-1 text-xs font-medium rounded bg-slate-100 border border-gray-300 mr-1">{{ $user->role->name }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700 text-sm">
                                    <a href="{{ route('dashboard.users.edit', $user->id) }}" class="inline-block bg-yellow-500 hover:bg-yellow-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('dashboard.users.destroy', $user->id) }}" method="POST" class="inline-block" id="delete-form-{{ $user->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded text-xs font-semibold">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    {{-- @else
                        @for($i=1;$i<=5;$i++)
                            <tr class="hover:bg-[#ecedf7] bg-white">
                                <td class="px-4 py-3 text-slate-700 text-sm">User {{ $i }}</td>
                                <td class="px-4 py-3 text-slate-700 text-sm">user{{ $i }}@mail.com</td>
                                <td class="px-4 py-3 text-slate-700 text-sm">08{{ rand(100000000,999999999) }}</td>
                                <td class="px-4 py-3 text-slate-700 text-sm">
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded bg-slate-100 border border-gray-300 mr-1">user</span>
                                </td>
                                <td class="px-4 py-3 text-slate-700 text-sm">
                                    <a href="#" class="inline-block bg-yellow-500 text-slate-900 px-3 py-1 rounded text-xs font-semibold mr-2"><i class="fas fa-edit"></i> Edit</a>
                                    <button class="bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold" disabled><i class="fas fa-trash"></i> Delete</button>
                                </td>
                            </tr>
                        @endfor --}}
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center justify-between text-slate-500 text-sm">
            <div>Showing <span class="text-slate-700">1</span> to <span class="text-slate-700">10</span> of <span class="text-slate-700">100</span> entries</div>
            @if(isset($users) && method_exists($users, 'links'))
                <div class="text-sm">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // AJAX Search untuk Users - tanpa page reload
    const searchInput = document.querySelector('#user-search-input');
    const searchBtn = document.querySelector('#user-search-btn');
    const tableBody = document.querySelector('#user-table-body');
    let debounceTimer = null;

    // Handle search on button click
    if (searchBtn) {
        searchBtn.addEventListener('click', (e) => {
            e.preventDefault();
            performSearch();
        });
    }

    // Handle search on input keyup
    if (searchInput) {
        searchInput.addEventListener('keyup', (e) => {
            // Perform search immediately on Enter
            if (e.key === 'Enter') {
                performSearch();
                return;
            }
            // Debounce untuk typing
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

        fetch(`{{ route("dashboard.users.index") }}?${params.toString()}`, {
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
            tableBody.innerHTML = `<tr><td colspan="5" class="px-4 py-3 text-center text-slate-400">Data tidak ditemukan</td></tr>`;
            return;
        }

        data.data.forEach((user) => {
            const roleHtml = user.role
                ? `<span class="inline-block px-2 py-1 text-xs font-medium rounded bg-slate-100 border border-gray-300 mr-1">${user.role.name}</span>`
                : `<span class="text-slate-400">-</span>`;

            const row = `
                <tr class="hover:bg-[#ecedf7] bg-white">
                    <td class="px-4 py-3 text-slate-700 text-sm">${user.name}</td>
                    <td class="px-4 py-3 text-slate-700 text-sm">${user.email}</td>
                    <td class="px-4 py-3 text-slate-700 text-sm">${user.phone_number || '-'}</td>
                    <td class="px-4 py-3 text-slate-700 text-sm">${roleHtml}</td>
                    <td class="px-4 py-3 text-slate-700 text-sm">
                        <a href="/dashboard/users/${user.id}/edit" class="inline-block bg-yellow-500 hover:bg-yellow-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button type="button" onclick="confirmDelete(${user.id}, '${user.name}')" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded text-xs font-semibold">
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
            `User "${userName}" akan dihapus secara permanen.`,
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

