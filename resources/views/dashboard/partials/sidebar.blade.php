<div class="h-full flex flex-col">
    {{-- Close button for mobile --}}
    <div class="md:hidden flex justify-end p-2">
        <button onclick="toggleSidebar(false)" class="text-slate-300 hover:text-white">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <div class="px-2 md:px-4 lg:px-6 py-3 md:py-4 border-b border-slate-700 flex flex-row justify-between items-center shrink-0">
        <h1 class="text-base md:text-lg lg:text-xl font-bold text-white flex items-center sidebar-label gap-2">
            <i class="fas fa-chart-line text-blue-400"></i>
            <span>Dashboard</span>
        </h1>
        <button onclick="toggleSidebarDesktop()" class="text-slate-300 hover:text-white focus:outline-none hidden md:block flex-shrink-0" id="desktopToggleBtn">
            <i class="fas fa-chevron-left text-lg"></i>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto px-2 md:px-3 lg:px-4 py-2 md:py-3">
        <ul class="space-y-1 md:space-y-2">
            <li>
                <a href="{{ route('dashboard.dash') }}" class="menu-item flex items-center gap-2 md:gap-3 px-2 md:px-4 py-2 md:py-3 rounded-lg text-sm md:text-base {{ request()->routeIs('dashboard.dash') ? 'active text-white' : 'text-slate-300' }}">
                    <i class="fas fa-home text-blue-400 flex-shrink-0"></i>
                    <span class="sidebar-label">Dashboard</span>
                </a>
            </li>
            @php
                $role = auth()->check() && auth()->user()->role ? auth()->user()->role->name : null;
            @endphp

            @if($role === 'Admin')
            <li>
                <a href="{{ route('dashboard.users.index') }}" class="menu-item flex items-center gap-2 md:gap-3 px-2 md:px-4 py-2 md:py-3 rounded-lg text-sm md:text-base {{ request()->routeIs('dashboard.users.*') ? 'active text-white' : 'text-slate-300' }}">
                    <i class="fas fa-users text-green-400 flex-shrink-0"></i>
                    <span class="sidebar-label">Users</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard.siswa') }}" class="menu-item flex items-center gap-2 md:gap-3 px-2 md:px-4 py-2 md:py-3 rounded-lg text-sm md:text-base {{ request()->routeIs('dashboard.siswa*') ? 'active text-white' : 'text-slate-300' }}">
                    <i class="fas fa-user-graduate text-blue-400 flex-shrink-0"></i>
                    <span class="sidebar-label">Siswa</span>
                </a>
            </li>
            @endif

            @if($role === 'Admin' || $role === 'Guru')
            <li>
                <a href="{{ route('dashboard.kelas.index') }}" class="menu-item flex items-center gap-2 md:gap-3 px-2 md:px-4 py-2 md:py-3 rounded-lg text-sm md:text-base {{ request()->routeIs('dashboard.kelas*') ? 'active text-white' : 'text-slate-300' }}">
                    <i class="fas fa-chalkboard-teacher text-yellow-400 flex-shrink-0"></i>
                    <span class="sidebar-label">Kelas</span>
                </a>
            </li>   
            <!-- <li>
                <a href="{{ route('dashboard.subjects.index') }}" class="menu-item flex items-center gap-2 md:gap-3 px-2 md:px-4 py-2 md:py-3 rounded-lg text-sm md:text-base {{ request()->routeIs('dashboard.subjects*') ? 'active text-white' : 'text-slate-300' }}">
                    <i class="fas fa-book text-green-400 flex-shrink-0"></i>
                    <span class="sidebar-label">Mata Pelajaran</span>
                </a>
            </li> -->
            <li>
                <a href="{{ route('dashboard.absensi') }}" class="menu-item flex items-center gap-2 md:gap-3 px-2 md:px-4 py-2 md:py-3 rounded-lg text-sm md:text-base {{ request()->routeIs('dashboard.absensi*') ? 'active text-white' : 'text-slate-300' }}">
                    <i class="fas fa-users text-green-400 flex-shrink-0"></i>
                    <span class="sidebar-label">Presensi Siswa</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard.jurnal') }}" class="menu-item flex items-center gap-2 md:gap-3 px-2 md:px-4 py-2 md:py-3 rounded-lg text-sm md:text-base {{ request()->routeIs('dashboard.jurnal*') ? 'active text-white' : 'text-slate-300' }}">
                    <i class="fas fa-book-open text-purple-400 flex-shrink-0"></i>
                    <span class="sidebar-label">Jurnal Siswa</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard.pelanggaran') }}" class="menu-item flex items-center gap-2 md:gap-3 px-2 md:px-4 py-2 md:py-3 rounded-lg text-sm md:text-base {{ request()->routeIs('dashboard.pelanggaran*') ? 'active text-white' : 'text-slate-300' }}">
                    <i class="fas fa-exclamation-triangle text-red-400 flex-shrink-0"></i>
                    <span class="sidebar-label">Pelanggaran Siswa</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard.izin') }}" class="menu-item flex items-center gap-2 md:gap-3 px-2 md:px-4 py-2 md:py-3 rounded-lg text-sm md:text-base {{ request()->routeIs('dashboard.izin*') ? 'active text-white' : 'text-slate-300' }}">
                    <i class="fas fa-file-text text-gray-400 flex-shrink-0"></i>
                    <span class="sidebar-label">Izin Siswa</span>
                </a>
            </li>
            @endif
        </ul>
    </nav>

    <div class="px-2 md:px-3 lg:px-4 py-2 md:py-3 border-t border-slate-700 shrink-0">
        <form method="POST" action="{{ route('logout.dash') }}">
            @csrf
            <button type="submit" class="menu-item flex items-center gap-2 md:gap-3 px-2 md:px-4 py-2 md:py-3 text-slate-300 rounded-lg hover:text-white w-full text-left hover:bg-red-500/20 text-sm md:text-base">
                <i class="fas fa-sign-out-alt text-red-400 flex-shrink-0"></i>
                <span class="sidebar-label">Logout</span>
            </button>
        </form>
    </div>
</div>

@push('scripts')
    <script>

    </script>
@endpush
