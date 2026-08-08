<!DOCTYPE html>
<html lang="en">
    <head>
        {{-- Header --}}
        @include("landing.partials.head")

        {{-- Additional Style --}}
        @stack("style")
    </head>
    <body class="relative w-full overflow-x-hidden bg-background">
        {{-- Navbar --}}

        <main class="relative flex w-full flex-col font-plusJakartaSans">
            {{-- Main Content --}}
            @yield("content")
        </main>

        {{-- Footer --}}
        @include('landing.partials.footer')

        {{-- Script --}}
        @include("landing.partials.script")

        {{-- Additional Script --}}
        @stack("script")
    </body>
</html>
