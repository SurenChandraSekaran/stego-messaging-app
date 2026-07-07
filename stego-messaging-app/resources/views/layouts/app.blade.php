<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ request()->is('chats*') ? 'h-full' : '' }}">
    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>   
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
        @wirechatStyles

        <style>
            :root, .dark {
                /* Sidebar Background (Conversations List & Search) */
                --wc-dark-secondary: #0f172a !important; /* Tailwind slate-900 */
                
                /* Main Windows Background (Chat Box Container & Footer Wrapper) */
                --wc-dark-primary: #020617 !important;   /* Tailwind slate-950 */
                
                /* Clean Borders to blend with Slate styling */
                --wc-dark-border: #1e293b !important;    /* Tailwind slate-800 */
            }
            /* ══ Force WireChat Panels to fill height and scroll independently ══ */
            @media (min-width: 640px) {
                    div[id^="wirechat"], 
                    .wirechat-wrapper, 
                    .wirechat-sidebar, 
                    .wirechat-main {
                        height: 100% !important;
                        overflow: hidden !important;
                    }
                }
        </style>
    </head>
    <body class="font-sans antialiased {{ request()->is('chats*') ? 'h-full overflow-hidden bg-slate-950' : '' }}">
        @include('layouts.navigation')

        @if(request()->is('chats*'))
            {{-- ── CHAT SYSTEM MODE: Locks viewport height to protect inputs from flying to the top ── --}}
            <div class="h-[calc(100vh-65px)] overflow-hidden bg-gray-100 dark:bg-slate-950 [&>*]:h-full">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        @else
            {{-- ── STANDARD APP MODE: Normal scrolling view layout for general pages ── --}}
            <div class="min-h-screen bg-gray-100 dark:bg-slate-950">
                @isset($header)
                    <header class="bg-white dark:bg-slate-900 shadow border-b border-slate-800/50">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot ?? '' }}
                    @yield('content')
                </main>
            </div>
        @endif

        @livewireScripts
        @wirechatAssets

        {{-- ══ Notification Engine: Toast + Payload Reveal Modal ══ --}}
        <div
            x-data="{
                toasts: [],
                payload: null,

                addToast(raw) {
                    const d = Array.isArray(raw) ? raw[0] : raw;
                    if (!d) return;
                    const id      = Date.now() + Math.random();
                    const type    = d.type || (d.icon === 'success' ? 'success' : d.icon === 'error' ? 'error' : 'info');
                    const title   = d.title   ?? null;
                    const message = d.message ?? d.text ?? '';
                    this.toasts.push({ id, type, title, message });
                    const self = this;
                    setTimeout(() => self.removeToast(id), 5000);
                },

                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                },

                showPayload(raw) {
                    const d = Array.isArray(raw) ? raw[0] : raw;
                    this.payload = d ? (d.message ?? d.text ?? '') : '';
                }
            }"
            @toast.window="addToast($event.detail)"
            @notify.window="addToast($event.detail)"
            @reveal-payload.window="showPayload($event.detail)"
            @swal.window="
                const d = Array.isArray($event.detail) ? $event.detail[0] : $event.detail;
                if (d && d.icon === 'success') { showPayload([d]); } else { addToast($event.detail); }
            "
        >
            {{-- Toast Stack (top-right) --}}
            <div class="fixed top-24 right-4 z-[9999] flex flex-col gap-2 w-80 pointer-events-none">

                {{-- System Notice Card --}}
                @if(cache('system_notice'))
                <div
                    x-data="{ dismissed: localStorage.getItem('dismissed_notice') === '{{ md5(cache('system_notice')) }}' }"
                    x-show="!dismissed"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 translate-x-6"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-6"
                    class="pointer-events-auto flex overflow-hidden bg-slate-900/95 backdrop-blur-sm border border-emerald-500/30 rounded-xl shadow-2xl shadow-black/60"
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
                        <button
                            @click="localStorage.setItem('dismissed_notice', '{{ md5(cache('system_notice')) }}'); dismissed = true"
                            class="shrink-0 ml-1 text-slate-600 hover:text-slate-300 transition-colors"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
                @endif

                <template x-for="toast in toasts" :key="toast.id">
                    <div
                        x-show="true"
                        x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0 translate-x-6"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-200 transform"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 translate-x-6"
                        class="pointer-events-auto flex overflow-hidden bg-slate-900 border border-slate-800 rounded-xl shadow-2xl shadow-black/50"
                    >
                        <div :class="{
                            'w-[3px] shrink-0 bg-emerald-500': toast.type === 'success',
                            'w-[3px] shrink-0 bg-rose-500':    toast.type === 'error' || toast.type === 'warning',
                            'w-[3px] shrink-0 bg-blue-500':    toast.type === 'info'
                        }"></div>

                        <div class="flex flex-1 items-start gap-3 p-3.5">
                            <div class="shrink-0 mt-0.5">
                                <template x-if="toast.type === 'success'">
                                    <div class="w-5 h-5 rounded-full bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </template>
                                <template x-if="toast.type === 'error' || toast.type === 'warning'">
                                    <div class="w-5 h-5 rounded-full bg-rose-500/15 border border-rose-500/30 flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </div>
                                </template>
                                <template x-if="toast.type === 'info'">
                                    <div class="w-5 h-5 rounded-full bg-blue-500/15 border border-blue-500/30 flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                </template>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p x-show="toast.title" x-text="toast.title" class="text-sm font-semibold text-white leading-snug"></p>
                                <p x-text="toast.message" class="text-xs text-slate-400 mt-0.5 leading-relaxed break-words"></p>
                            </div>

                            <button @click="removeToast(toast.id)" class="shrink-0 ml-1 text-slate-600 hover:text-slate-300 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Payload Reveal Modal --}}
            <div
                x-show="payload !== null"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @keydown.escape.window="payload = null"
                class="fixed inset-0 z-[9998] flex items-center justify-center"
                style="display: none;"
            >
                <div @click="payload = null" class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

                <div
                    x-show="payload !== null"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative z-10 w-full max-w-lg mx-4"
                >
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-[0_0_60px_rgba(52,211,153,0.08)] overflow-hidden">
                        <div class="flex items-center gap-3 px-5 py-3.5 border-b border-slate-800">
                            <div class="flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">Payload Decrypted</p>
                                <p class="text-[10px] text-emerald-500/70 font-mono tracking-widest uppercase">AES-256 // LSB BITSTREAM VERIFIED</p>
                            </div>
                            <button @click="payload = null" class="ml-auto text-slate-600 hover:text-slate-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="px-5 py-4">
                            <div class="bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 max-h-72 overflow-y-auto">
                                <p x-text="payload" class="font-mono text-sm text-emerald-400 whitespace-pre-wrap break-words leading-relaxed"></p>
                            </div>
                        </div>

                        <div class="px-5 pb-4 flex justify-end">
                            <button
                                @click="payload = null"
                                class="px-4 py-1.5 text-[10px] font-mono font-semibold uppercase tracking-widest text-slate-400 bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 hover:text-white transition-all duration-150"
                            >[ CLOSE SESSION ]</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>