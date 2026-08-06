<header class="bg-slate-800/30 backdrop-blur-sm border-b border-slate-700 px-2 sm:px-4 md:px-6 py-2 md:py-4 shrink-0">
    <div class="flex items-center justify-between gap-2 md:gap-4">
        <div class="flex items-center gap-2 md:gap-3 min-w-0">
            <button class="md:hidden text-white focus:outline-none flex-shrink-0" onclick="toggleSidebar(true)">
                <i class="fas fa-bars text-lg md:text-xl"></i>
            </button>

            <h2 id="pageTitle" class="text-base md:text-2xl font-semibold text-white truncate">Dashboard</h2>
        </div>
        <div class="flex items-center gap-2 md:gap-4 min-w-0">
            <div class="text-slate-300 flex items-center gap-2 md:gap-3 flex-shrink-0">
                <i class="fas fa-bell text-sm md:text-base"></i>
                @if(auth()->check())
                    <span class="text-xs md:text-sm hidden sm:inline">
                        Selamat, <span class="text-white font-semibold">{{ auth()->user()->name }}</span>!
                    </span>
                    <span class="text-xs text-slate-400 hidden md:inline">
                        ({{ auth()->user()->role ? auth()->user()->role->name : '-' }})
                    </span>
                @endif
            </div>
            <div class="w-7 md:w-8 h-7 md:h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user text-white text-xs md:text-sm"></i>
            </div>
        </div>
    </div>
</header>
