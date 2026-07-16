<header class="bg-surface dark:bg-surface-dim shadow-sm docked full-width top-0 sticky z-50">
    <!-- Desktop & Tablet Nav -->
    <nav class="hidden md:flex justify-between items-center w-full px-md md:px-margin-desktop h-20 max-w-7xl mx-auto">
        <div class="flex items-center gap-base">
            <a href="{{ route('landing.home') }}" class="flex items-center gap-base">
                <span class="material-symbols-outlined text-primary text-3xl"
                    style="font-variation-settings: 'FILL' 1;">shape_recognition</span>
                <span
                    class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed">ManaMukanya</span>
            </a>
        </div>
        <div class="hidden md:flex gap-lg items-center">
            <a class="font-body-md text-body-md {{ request()->routeIs('landing.home') ? 'text-primary dark:text-primary-fixed font-bold border-b-2 border-primary dark:border-primary-fixed pb-1' : 'text-on-surface-variant dark:text-outline-variant hover:text-primary transition-colors'}}"
                href="{{ route('landing.home') }}">Home</a>
            <a class="font-body-md text-body-md {{ request()->routeIs('feature.absen') ? 'text-primary dark:text-primary-fixed font-bold border-b-2 border-primary dark:border-primary-fixed pb-1' : 'text-on-surface-variant dark:text-outline-variant hover:text-primary transition-colors'}}"
                href="{{ route('feature.absen') }}">Absen</a>
            <a class="font-body-md text-body-md {{ request()->routeIs('feature.izin') ? 'text-primary dark:text-primary-fixed font-bold border-b-2 border-primary dark:border-primary-fixed pb-1' : 'text-on-surface-variant dark:text-outline-variant hover:text-primary transition-colors'}}"
                href="{{ route('feature.izin') }}">Izin</a>
            <a class="font-body-md text-body-md {{ request()->routeIs('feature.jurnal') ? 'text-primary dark:text-primary-fixed font-bold border-b-2 border-primary dark:border-primary-fixed pb-1' : 'text-on-surface-variant dark:text-outline-variant hover:text-primary transition-colors'}}"
                href="{{ route('feature.jurnal') }}">Jurnal</a>
        </div>
        <div class="flex items-center gap-md">
            @if(auth()->check())
                <div class="relative flex items-center space-x-2 group" id="profileDropdown">
                    <button type="button" id="profileButton" class="flex items-center gap-md focus:outline-none">
                        <div class="text-right hidden sm:block">
                            <p class="font-label-md text-label-md font-bold text-on-surface">{{ auth()->user()->name }}</p>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">
                                {{ auth()->user()->classes()->first()->name ?? 'Siswa' }}</p>
                        </div>
                        <div
                            class="w-10 h-10 rounded-full overflow-hidden border-2 border-primary-fixed flex items-center justify-center bg-primary-fixed">
                            <span class="material-symbols-outlined text-primary text-2xl">account_circle</span>
                        </div>
                    </button>
                    <!-- Dropdown menu -->
                    <div id="dropdownMenu"
                        class="absolute right-0 top-12 min-w-[150px] bg-white border border-outline-variant rounded-xl shadow-lg py-2 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50">
                        <a href="{{ route('landing.profile') }}"
                            class="block px-4 py-2 text-sm text-on-surface hover:bg-surface-container-low transition">Profile</a>
                        <form action="{{ route('auth.logoutsiswa') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-on-surface hover:bg-surface-container-low transition">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="flex gap-xs">
                    <a href="{{ route('auth.login-register') }}"
                        class="bg-surface-container-low hover:bg-surface-container-high text-primary font-label-md text-label-md px-md py-xs rounded-lg transition-all border border-outline-variant">Login</a>
                    <a href="{{ route('auth.login-register', ['panel' => 'register']) }}"
                        class="bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md px-md py-xs rounded-lg transition-all">Register</a>
                </div>
            @endif
        </div>
    </nav>

    <!-- Mobile Nav -->
    <nav class="flex md:hidden justify-between items-center w-full px-md h-20 mx-auto">
        <div class="flex items-center gap-base">
            <a href="{{ route('landing.home') }}" class="flex items-center gap-base">
                <span class="material-symbols-outlined text-primary text-3xl"
                    style="font-variation-settings: 'FILL' 1;">shape_recognition</span>
                <span
                    class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed">ManaMukanya</span>
            </a>
        </div>
        <div class="flex items-center gap-base">
            @if(auth()->check())
                <div class="relative flex items-center space-x-2 group" id="profileDropdownMobile">
                    <button type="button" id="profileButtonMobile"
                        class="w-10 h-10 rounded-full overflow-hidden border-2 border-primary-container flex items-center justify-center bg-primary-fixed focus:outline-none">
                        <span class="material-symbols-outlined text-primary text-2xl">account_circle</span>
                    </button>
                    <!-- Dropdown menu -->
                    <div id="dropdownMenuMobile"
                        class="absolute right-0 top-12 min-w-[150px] bg-white border border-outline-variant rounded-xl shadow-lg py-2 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50">
                        <a href="{{ route('landing.profile') }}"
                            class="block px-4 py-2 text-sm text-on-surface hover:bg-surface-container-low transition">Profile</a>
                        <form action="{{ route('auth.logoutsiswa') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-on-surface hover:bg-surface-container-low transition">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="flex gap-xs">
                    <a href="{{ route('auth.login-register') }}"
                        class="bg-surface-container-low hover:bg-surface-container-high text-primary font-label-md text-label-md px-md py-xs rounded-lg transition-all border border-outline-variant">Login</a>
                </div>
            @endif
        </div>
    </nav>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Setup for Desktop Dropdown
        const profileButton = document.getElementById('profileButton');
        const dropdownMenu = document.getElementById('dropdownMenu');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profileButton && dropdownMenu) {
            profileButton.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('opacity-0');
                dropdownMenu.classList.toggle('pointer-events-none');
            });
            document.addEventListener('click', function (e) {
                if (profileDropdown && !profileDropdown.contains(e.target)) {
                    dropdownMenu.classList.add('opacity-0');
                    dropdownMenu.classList.add('pointer-events-none');
                }
            });
            dropdownMenu.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        // Setup for Mobile Dropdown
        const profileButtonMobile = document.getElementById('profileButtonMobile');
        const dropdownMenuMobile = document.getElementById('dropdownMenuMobile');
        const profileDropdownMobile = document.getElementById('profileDropdownMobile');

        if (profileButtonMobile && dropdownMenuMobile) {
            profileButtonMobile.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdownMenuMobile.classList.toggle('opacity-0');
                dropdownMenuMobile.classList.toggle('pointer-events-none');
            });
            document.addEventListener('click', function (e) {
                if (profileDropdownMobile && !profileDropdownMobile.contains(e.target)) {
                    dropdownMenuMobile.classList.add('opacity-0');
                    dropdownMenuMobile.classList.add('pointer-events-none');
                }
            });
            dropdownMenuMobile.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }
    });
</script>
