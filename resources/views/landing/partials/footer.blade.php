<!-- Desktop Footer -->
<footer
    class="hidden md:flex bg-surface-container-lowest dark:bg-surface-container-lowest border-t border-outline-variant dark:border-outline w-full py-lg px-md md:px-margin-desktop mt-auto flex-col md:flex-row justify-between items-center gap-base">
    <div class="flex flex-col md:flex-row items-center gap-lg">
        <div class="flex items-center gap-xs">
            <span class="material-symbols-outlined text-primary"
                style="font-variation-settings: 'FILL' 1;">shape_recognition</span>
            <span class="font-label-md text-label-md font-bold text-on-surface">SIFARECO</span>
        </div>
        <p class="font-label-sm text-label-sm text-on-surface-variant">&copy; {{ date('Y') }} SIFARECO. All rights
            reserved.</p>
    </div>
    <div class="flex gap-lg">
        <a class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors"
            href="#">Contact School</a>
        <a class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors"
            href="#">Privacy Policy</a>
        <a class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors"
            href="#">Terms of Service</a>
    </div>
</footer>

<!-- Mobile Footer -->
<footer
    class="flex md:hidden w-full py-lg px-md mt-auto flex-col items-center gap-base bg-surface-container-lowest border-t border-outline-variant dark:border-outline mb-20">
    <span class="font-label-md text-label-md font-bold text-on-surface">SIFARECO</span>
    <p class="font-label-sm text-label-sm text-on-surface-variant">&copy; {{ date('Y') }} SIFARECO. All rights
        reserved.</p>
    <div class="flex gap-md">
        <a class="text-on-surface-variant dark:text-outline-variant hover:text-primary transition-colors font-label-sm text-label-sm"
            href="#">Contact School</a>
        <a class="text-on-surface-variant dark:text-outline-variant hover:text-primary transition-colors font-label-sm text-label-sm"
            href="#">Privacy Policy</a>
    </div>
</footer>