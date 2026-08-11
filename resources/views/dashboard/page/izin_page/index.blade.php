@extends(
    "dashboard.layout.app",
    [
        "title" => "Izin Siswa",
    ]
)

@section('content')
    <div class="content-section">
        <div class="backdrop-blur-sm rounded-xl px-6 mb-6 flex flex-row items-center justify-between">
            <div class="flex items-center gap-4">
                <i class="fas fa-file-text text-4xl text-gray-400"></i>
                <div>
                    <h3 class="text-2xl font-semibold text-black">Daftar Pengajuan Izin</h3>
                    <p class="text-slate-700">Daftar izin siswa — tanggal, jam, status, dan lokasi.</p>
                </div>
            </div>
            <div class="flex items-center">
                <a href="{{ route('dashboard.izin.exportExcel') }}" class="bg-gray-600 hover:bg-gray-500 text-white px-3 py-2 rounded text-sm"><i class="fa fa-file-download"></i> Ekspor Excel</a>
            </div>
        </div>

        @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session("success") }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            </script>
        @endif

        <div class="backdrop-blur-sm rounded-xl p-4 border border-gray-300">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
                <form id="izin-filter-form" action="{{ route('dashboard.izin') }}" method="GET" class="w-full flex flex-col md:flex-row md:items-center md justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <label class="text-slate-700 text-sm">Show</label>
                        <select name="per_page" id="izin-per-page" class="bg-white text-slate-700 border border-gray-300 rounded p-1 text-sm text-center">
                            @foreach([10, 25, 50, 100] as $option)
                                <option value="{{ $option }}" {{ isset($perPage) && $perPage == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <input type="search" name="search" id="izin-search-input" value="{{ $search ?? '' }}" placeholder="Cari nama siswa / pelajaran" class="flex-1 md:flex-none bg-white text-slate-700 border border-slate-700 rounded px-3 py-2 text-sm" />
                        <button type="submit" class="bg-gray-600 hover:bg-gray-500 text-white px-3 py-2 rounded text-sm">Search</button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto -mx-4">
                <table class="min-w-full table-auto border-collapse border border-gray-300">
                    <thead>
                        <tr class="text-left text-slate-700 text-sm uppercase tracking-wider bg-[#ecedf7] border border-gray-300">
                            <th class="px-4 py-3 w-12">No</th>
                            <th class="px-4 py-3 col-name">Nama Siswa</th>
                            <th class="px-4 py-3">Hari, Tanggal</th>
                            <th class="px-4 py-3">Jenis Izin</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 w-1/6">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-300" id="izin-table-body">
                        @if(isset($permissions) && $permissions->count())
                            @foreach($permissions as $idx => $p)
                                @php
                                    $no = (isset($permissions) && method_exists($permissions, 'firstItem')) ? $permissions->firstItem() + $loop->index : $loop->iteration;
                                @endphp
                                <tr class="hover:bg-[#ecedf7] bg-white border border-gray-300">
                                    <td class="px-4 py-3 text-slate-700 text-sm align-center">{{ $no }}</td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-purple-700 flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($p->student->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="view-student text-slate-700 font-medium text-sm">{{ $p->student->name }}</div>
                                                <div class="text-slate-700 text-xs">{{ $p->student_id ? 'ID: '.$p->student_id : '' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 text-sm">
                                        <div class="font-medium">{{ $p->created_at->format('Y-m-d H:i') }}</div>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 text-sm">
                                        <div class="text-sm text-slate-700 line-clamp-2">
                                            {{ $p->type ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 text-sm">
                                        <div class="text-sm text-slate-700 line-clamp-2 flex flex-row justify-start">
                                            @if ($p->status == 'approved')
                                                <div class="px-2 py-1 bg-green-400/30 border-2 border-green-500 rounded-xl text-green-800 text-center font-semibold">Approved</div>
                                            @elseif ($p->status == 'pending')
                                                <div class="px-2 py-1 bg-yellow-400/30 border-2 border-yellow-500 rounded-xl text-yellow-800 text-center font-semibold">Pending</div>
                                            @else
                                                <div class="px-2 py-1 bg-red-400/30 border-2 border-red-500 rounded-xl text-red-800 text-center font-semibold">Rejected</div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 text-sm flex">
                                        <a href="{{ route('dashboard.izin.show', $p->id) }}" class="inline-block bg-blue-500 hover:bg-blue-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>

                                        <button type="button" data-id="{{ $p->id }}" onclick="openStatusModal({{ $p->id }})" class="inline-block bg-yellow-500 hover:bg-yellow-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                                            <i class="fas fa-edit"></i> Status
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="hover:bg-slate-800/40">
                                <td colspan="6" class="px-4 py-3 text-slate-700 text-sm text-center">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center justify-between text-slate-600 text-sm">
                <div>Showing <span class="text-slate-700">{{ $permissions->firstItem() ?? 0 }}</span> to <span class="text-slate-700">{{ $permissions->lastItem() ?? 0 }}</span> entries</div>
                @if(isset($permissions) && method_exists($permissions, 'links'))
                    <div class="text-sm">
                        {{ $permissions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Edit Status -->
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden" id="editStatusModal">
        <div class="bg-[#ecedf7] backdrop-blur-sm rounded-xl p-6 border border-slate-700 flex flex-col items-center gap-4">
            <div class="flex items-center gap-4">
                <i class="fas fa-edit text-4xl text-yellow-400"></i>
                <div>
                    <h3 class="text-2xl font-semibold text-black">Edit Status Izin</h3>
                    <p class="text-slate-700">Pilih status baru untuk izin ini.</p>
                </div>
            </div>
            <form id="statusForm" action="" method="POST" class="w-full">
                @csrf
                <input type="hidden" id="permissionId" name="permission_id" value="">
                <div class="mb-4">
                    <label for="status" class="block text-slate-700 mb-1">Status</label>
                    <select id="status" name="status" required class="w-full bg-white text-slate-700 border border-slate-700 rounded px-3 py-2">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-500 text-white px-4 py-2 rounded text-sm font-semibold">OK</button>
                    <button type="button" class="bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded text-sm font-semibold" id="closeModal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('izin-filter-form');
        const perPage = document.getElementById('izin-per-page');

        if (perPage) {
            perPage.addEventListener('change', function () {
                form.submit();
            });
        }

        const closeModalButton = document.getElementById('closeModal');
        if (closeModalButton) {
            closeModalButton.addEventListener('click', function() {
                document.getElementById('editStatusModal').classList.add('hidden');
            });
        }
    });

    // Fungsi untuk membuka modal edit status
    function openStatusModal(id) {
        document.getElementById('permissionId').value = id;
        document.getElementById('statusForm').action = `/dashboard/izin/${id}/update-status`; // Set action form
        document.getElementById('editStatusModal').classList.remove('hidden');
    }
</script>
@endpush
