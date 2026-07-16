<!-- Bottom Navigation Bar (Mobile Primary Shell) -->
  <nav class="fixed bottom-0 left-0 right-0 bg-surface dark:bg-surface-dim px-md py-base flex justify-around items-center h-20 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] md:hidden z-50">
    @if(request()->routeIs('landing.home'))
        <a href="{{ route('landing.home') }}" class="flex flex-col items-center gap-xs text-primary font-bold transition-transform active:scale-95">
        <span class="material-symbols-outlined !text-[24px]" style="font-variation-settings: 'FILL' 1">home</span>
        <span class="font-label-sm text-label-sm">Home</span>
        <div class="w-1 h-1 bg-primary rounded-full mt-0.5"></div>
        </a>
    @else
        <a href="{{ route('landing.home') }}" class="flex flex-col items-center gap-xs text-on-surface-variant dark:text-outline-variant hover:text-primary transition-all active:scale-95">
        <span class="material-symbols-outlined !text-[24px]" style="font-variation-settings: 'FILL' 1">home</span>
        <span class="font-label-sm text-label-sm">Home</span>
        </a>
    @endif

    @if(request()->routeIs('feature.absen'))
        <a href="{{ route('feature.absen') }}" class="flex flex-col items-center gap-xs text-primary font-bold transition-transform active:scale-95">
        <span class="material-symbols-outlined !text-[24px]">shape_recognition</span>
        <span class="font-label-sm text-label-sm">Absen</span>
        <div class="w-1 h-1 bg-primary rounded-full mt-0.5"></div>
        </a>
    @else
        <a href="{{ route('feature.absen') }}" class="flex flex-col items-center gap-xs text-on-surface-variant dark:text-outline-variant hover:text-primary transition-all active:scale-95">
        <span class="material-symbols-outlined !text-[24px]">shape_recognition</span>
        <span class="font-label-sm text-label-sm">Absen</span>
        </a>
    @endif

    @if(request()->routeIs('feature.izin'))
        <a href="{{ route('feature.izin') }}" class="flex flex-col items-center gap-xs text-primary font-bold transition-transform active:scale-95">
        <span class="material-symbols-outlined !text-[24px]">description</span>
        <span class="font-label-sm text-label-sm">Izin</span>
        <div class="w-1 h-1 bg-primary rounded-full mt-0.5"></div>
        </a>
    @else
        <a href="{{ route('feature.izin') }}" class="flex flex-col items-center gap-xs text-on-surface-variant dark:text-outline-variant hover:text-primary transition-all active:scale-95">
        <span class="material-symbols-outlined !text-[24px]">description</span>
        <span class="font-label-sm text-label-sm">Izin</span>
        </a>
    @endif

    @if(request()->routeIs('landing.profile'))
        <a href="{{ route('landing.profile') }}" class="flex flex-col items-center gap-xs text-primary font-bold transition-transform active:scale-95">
        <span class="material-symbols-outlined !text-[24px]">person</span>
        <span class="font-label-sm text-label-sm">Profil</span>
        <div class="w-1 h-1 bg-primary rounded-full mt-0.5"></div>
        </a>
    @else
        <a href="{{ route('landing.profile') }}" class="flex flex-col items-center gap-xs text-on-surface-variant dark:text-outline-variant hover:text-primary transition-all active:scale-95">
        <span class="material-symbols-outlined !text-[24px]">person</span>
        <span class="font-label-sm text-label-sm">Profil</span>
        </a>
    @endif
  </nav>
