@extends(
    "dashboard.layout.app",
    [
        "title" => "Kelas",
    ]
)

@section('content')
<div class="content-section">
    <div class="flex flex-row items-center justify-between backdrop-blur-sm rounded-xl px-6 mb-6">
        <div class="flex items-center gap-4">
            <i class="fas fa-chalkboard-teacher text-4xl text-green-400"></i>
            <div>
                <h3 class="text-2xl font-semibold">Manajemen Kelas</h3>
                <p class="text-slate-700">Daftar Kelas.</p>
            </div>
        </div>
        @php
            $role = auth()->check() && auth()->user()->role ? auth()->user()->role->name : null;
        @endphp
        @if($role === 'Admin')
        <div>
            <a href="{{ route('dashboard.kelas.create') }}">
                <button type="button" class="bg-green-600 hover:bg-green-500 text-white px-3 py-2 rounded text-sm"><i class="fa fa-plus"></i> Tambah Kelas</button>
            </a>
        </div>
        @endif
    </div>

    <div class="backdrop-blur-sm rounded-xl p-4 border border-gray-300">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <div class="flex items-center gap-3">
                <label class="text-slate-700 text-sm">Show</label>
                <select id="kelas-per-page" class="bg-white text-slate-700 border border-gray-300 rounded p-1 my-1 text-sm text-center">
                    @foreach([10, 25, 50, 100] as $option)
                        <option value="{{ $option }}" {{ isset($perPage) && $perPage == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="overflow-x-auto -mx-4">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead>
                    <tr class="text-left text-slate-700 text-sm uppercase tracking-wider bg-[#ecedf7] border border-gray-300">
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Wali Kelas</th>
                        <th class="px-4 py-3 w-[200px]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                    @if(isset($classes) && $classes->count())
                        @foreach($classes as $class)
                            <tr class="hover:bg-[#ecedf7] bg-white">
                                <td class="px-4 py-3 text-slate-700 text-sm">{{ $class->name }}</td>
                                <td class="px-4 py-3 text-slate-700 text-sm">
                                    {{ $class->walas ? $class->walas->name : '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-700 text-sm">
                                    <a href="{{ route('dashboard.kelas.show', $class->id) }}" class="inline-block bg-blue-500 hover:bg-blue-400 text-white px-3 py-1 rounded text-xs font-semibold mr-2">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    @php
                                        $role = auth()->check() && auth()->user()->role ? auth()->user()->role->name : null;
                                    @endphp
                                    @if($role === 'Admin')
                                    <form id="delete-form-{{ $class->id }}" action="{{ route('dashboard.kelas.destroy', $class->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('{{ $class->id }}', '{{ $class->name }}')" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded text-xs font-semibold">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-slate-700 text-center">Tidak ada data kelas.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if(isset($classes) && method_exists($classes, 'links'))
            <div class="mt-4 flex flex-col md:flex-row md:items-center md:justify-between text-slate-600 text-sm gap-2">
                <div>Showing <span class="text-slate-700">{{ $classes->firstItem() }}</span> to <span class="text-slate-700">{{ $classes->lastItem() }}</span> of <span class="text-slate-700">{{ $classes->total() }}</span> entries</div>
                <div class="text-sm">
                    {{ $classes->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            showSuccess('{{ session("success") }}', 'Berhasil!');
        });
    @endif

    document.addEventListener('DOMContentLoaded', function() {
        const perPageSelect = document.getElementById('kelas-per-page');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                const params = new URLSearchParams(window.location.search);
                params.set('per_page', this.value);
                params.delete('page');
                window.location.search = params.toString();
            });
        }
    });

    function confirmDelete(id, className) {
        showConfirm(
            `Kelas "${className}" akan dihapus secara permanen.`,
            'Yakin ingin menghapus?',
            function() {
                document.getElementById('delete-form-' + id).submit();
            }
        );
    }
</script>
@endpush
