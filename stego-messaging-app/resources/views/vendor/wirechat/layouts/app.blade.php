<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
@php
    $currentPanel= \Wirechat\Wirechat\Facades\Wirechat::currentPanel();
    $title = $currentPanel->getHeading()?? config('app.name', 'Laravel');
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

      <!--THEME:--ADD TO TOP OT PREVENT FLICKERING -->
      <script>

         /* Function to apply or remove the dark theme */
        function updateTheme(isDark) {
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        /* Check the initial theme preference */
        const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        updateTheme(darkModeMediaQuery.matches);

        /* listen to changed in (prefers-color-scheme: dark) */
        darkModeMediaQuery.addEventListener('change', (event) => {
            updateTheme(event.matches);
        });

        /* Add This to update theme when page is wire navigated */
        document.addEventListener('livewire:navigated', () => {
          const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
          updateTheme(darkModeMediaQuery.matches);  // Re-apply the theme based on system preference
         });
      </script>

    {{--Set up Favicon--}}
    @if($currentPanel->hasFavicon())
        <link rel="icon" href="{{ $currentPanel->getFavicon() }}" />
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @wirechatStyles(panel: $panel)
</head>

<body  x-data x-cloak class="font-sans antialiased">
    @include('layouts.navigation')
    <div class="min-h-screen bg-[var(--wc-light-primary)] dark:bg-[var(--wc-dark-primary)]">
        <!-- Page Content -->
        <main class="h-[calc(100vh_-_0.0rem)]">
             @yield('content',$slot??null)
        </main>

    </div>

    @livewireScripts
    @wirechatAssets(panel: $panel)

    @if(cache('system_notice'))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-x-6"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-6"
        class="fixed top-24 right-4 z-[9999] w-80 pointer-events-auto flex overflow-hidden bg-slate-900/95 backdrop-blur-sm border border-emerald-500/30 rounded-xl shadow-2xl shadow-black/60"
        style="display: none;"
    >
        <div class="w-[3px] shrink-0 bg-emerald-500"></div>
        <div class="flex flex-1 items-start gap-3 p-3.5">
            <div class="shrink-0 mt-0.5">
                <div class="w-5 h-5 rounded-full bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center">
                    <svg class="w-2.5 h-2.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-mono font-semibold text-emerald-500/70 uppercase tracking-widest leading-none">System Notice</p>
                <p class="text-xs text-emerald-400 mt-1 leading-relaxed break-words">{{ cache('system_notice') }}</p>
            </div>
            <button @click="show = false" class="shrink-0 ml-1 text-slate-600 hover:text-slate-300 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    @endif

{{--    <script>--}}
{{--        document.addEventListener('livewire:updated', function () {--}}
{{--            document.querySelectorAll('img[src]').forEach(img => {--}}
{{--                const src = img.getAttribute('src');--}}
{{--                const svg = img.nextElementSibling;--}}
{{--                if (src) {--}}
{{--                    const preloadImg = new Image();--}}
{{--                    preloadImg.src = src;--}}
{{--                    preloadImg.onload = () => {--}}
{{--                        img.style.display = 'inline-flex';--}}
{{--                        svg.style.display = 'none';--}}
{{--                    };--}}
{{--                    preloadImg.onerror = () => {--}}
{{--                        img.style.display = 'none';--}}
{{--                        svg.style.display = 'inline-flex';--}}
{{--                    };--}}
{{--                } else {--}}
{{--                    img.style.display = 'none';--}}
{{--                    svg.style.display = 'inline-flex';--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
</body>

</html>
