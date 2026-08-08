@extends(
    "dashboard.layout.app",
    [
        "title" => "Kelas",
    ]
)

@section('content')
<div class="content-section">
    <div class="bg-slate-800/50 flex flex-row items-center justify-between backdrop-blur-sm rounded-xl p-6 border border-slate-700 mb-6">
        <div class="flex items-center gap-4">
            <i class="fas fa-chalkboard-teacher text-4xl text-green-400"></i>
            <div>
                <h3 class="text-2xl font-semibold text-white">Kelas</h3>
                <p class="text-slate-400">Daftar Kelas.</p>
            </div>
        </div>
        @php
            $role = auth()->check() && auth()->user()->role ? auth()->user()->role->name : null;
        @endphp
        @if($role === 'Admin')
        <div>
            <a href="{{ route('dashboard.kelas.create') }}">
                <button type="button" class="bg-green-600 hover:bg-green-500 text-white px-3 py-2 rounded text-sm">Tambah Kelas</button>
            </a>
        </div>
        @endif
    </div>

    <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700">
        <div class="overflow-x-auto -mx-4 px-4">
            <table class="min-w-full table-auto border-collapse">
                <thead>
                    <tr class="text-left text-slate-300 text-sm uppercase tracking-wider">
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Wali Kelas</th>
                        <th class="px-4 py-3 w-[200px]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @if(isset($classes) && $classes->count())
                        @foreach($classes as $class)
                            <tr class="hover:bg-slate-800/40">
                                <td class="px-4 py-3 text-slate-200 text-sm">{{ $class->name }}</td>
                                <td class="px-4 py-3 text-slate-200 text-sm">
                                    {{ $class->walas ? $class->walas->name : '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-200 text-sm">
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
                            <td colspan="3" class="px-4 py-3 text-slate-400 text-center">Tidak ada data kelas.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if(isset($classes) && method_exists($classes, 'links'))
            <div class="mt-4 flex items-center justify-between text-slate-400 text-sm">
                <div>Showing <span class="text-white">{{ $classes->firstItem() }}</span> to <span class="text-white">{{ $classes->lastItem() }}</span> of <span class="text-white">{{ $classes->total() }}</span> entries</div>
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
