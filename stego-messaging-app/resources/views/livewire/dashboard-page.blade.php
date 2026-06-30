{{-- Root div carries wire:poll.5s — re-executes Dashboard::render() every 5 s --}}
<div wire:poll.5s class="bg-slate-950 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">


        {{-- ════════════════════════════════════════════════════════════
             SECTION 1 — Hero Greeting
        ════════════════════════════════════════════════════════════ --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[11px] font-mono font-medium text-emerald-400 tracking-widest uppercase">System Operational</span>
                </div>

                <h1 class="text-2xl font-extrabold tracking-tight text-white">
                    Welcome back, <span class="text-blue-400">{{ $user->name }}</span>
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    StegChat Command Center &mdash; {{ now()->format('l, F j, Y') }}
                </p>
            </div>

            <a href="/chats"
               class="self-start sm:self-auto group flex items-center gap-2.5 px-4 py-2.5
                      bg-blue-600/10 text-blue-400 border border-blue-500/25 rounded-xl
                      text-sm font-semibold
                      hover:bg-blue-600/20 hover:border-blue-400/50 hover:text-blue-300
                      transition-all duration-150">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
                Open Secure Chats
                @if($unreadCount > 0)
                    <span class="flex items-center justify-center min-w-[20px] h-5 px-1 text-[10px] font-bold text-white bg-blue-500 rounded-full">
                        {{ $unreadCount }}
                    </span>
                @endif
            </a>
        </div>


        {{-- ════════════════════════════════════════════════════════════
             SYSTEM NOTICE BOARD
             wire:ignore prevents Livewire's 5s poll morph from stripping
             this static element. wire:key lets morphdom track it correctly
             if the outer ignore is ever removed.
        ════════════════════════════════════════════════════════════ --}}
        @if(cache('system_notice'))
        <div wire:ignore wire:key="dashboard-notice-board">
            <div class="flex overflow-hidden bg-slate-900 border border-emerald-500/30 rounded-xl shadow-lg shadow-black/30">
                <div class="w-1 shrink-0 bg-emerald-500"></div>
                <div class="flex flex-1 items-start gap-3.5 px-4 py-3.5">
                    <div class="shrink-0 mt-0.5 flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-mono font-semibold text-emerald-500/70 uppercase tracking-widest leading-none">System Notice</p>
                        <p class="mt-1 text-sm text-emerald-400 leading-relaxed break-words">{{ cache('system_notice') }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif


        {{-- ════════════════════════════════════════════════════════════
             SECTION 2 — Cryptographic Stat Cards (live-polling component)
        ════════════════════════════════════════════════════════════ --}}
        <livewire:dashboard-stats />


        {{-- ════════════════════════════════════════════════════════════
             SECTION 3 — Main Asymmetric Grid
        ════════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-8">


            {{-- ── Left Column (2/3) ──────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-6">

                <livewire:friends-list />

                {{-- Quick Launch Grid --}}
                <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-3.5 h-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                        <h3 class="text-sm font-semibold text-white">Quick Launch</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-3">

                        {{-- Secure Chats --}}
                        <a href="/chats"
                           class="group flex items-center gap-3 p-3.5 rounded-lg border border-slate-800/60
                                  hover:border-slate-700/80 hover:bg-slate-800/40 transition-all duration-150">
                            <div class="shrink-0 flex items-center justify-center w-9 h-9 rounded-lg
                                        bg-blue-500/10 border border-blue-500/20
                                        group-hover:bg-blue-500/20 transition-colors">
                                <svg class="w-4 h-4 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-white">Secure Chats</p>
                                @if($unreadCount > 0)
                                    <p class="text-xs text-blue-400 font-medium">{{ $unreadCount }} unread</p>
                                @else
                                    <p class="text-xs text-slate-500">All caught up</p>
                                @endif
                            </div>
                            @if($unreadCount > 0)
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                                </span>
                            @endif
                        </a>

                        {{-- Incoming Requests --}}
                        <div class="group flex items-center gap-3 p-3.5 rounded-lg border
                                    {{ $pendingIncoming->count() > 0 ? 'border-blue-500/20 bg-blue-500/5' : 'border-slate-800/60' }}
                                    hover:border-slate-700/80 hover:bg-slate-800/40 transition-all duration-150">
                            <div class="shrink-0 flex items-center justify-center w-9 h-9 rounded-lg
                                        {{ $pendingIncoming->count() > 0 ? 'bg-blue-500/15 border-blue-500/30' : 'bg-slate-800/60 border-slate-700/40' }}
                                        border transition-colors">
                                <svg class="w-4 h-4 {{ $pendingIncoming->count() > 0 ? 'text-blue-400' : 'text-slate-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-white">Requests</p>
                                @if($pendingIncoming->count() > 0)
                                    <p class="text-xs text-blue-400 font-medium">{{ $pendingIncoming->count() }} pending handshake{{ $pendingIncoming->count() > 1 ? 's' : '' }}</p>
                                @else
                                    <p class="text-xs text-slate-500">No new requests</p>
                                @endif
                            </div>
                            @if($pendingIncoming->count() > 0)
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                                </span>
                            @endif
                        </div>

                        {{-- My Profile --}}
                        <a href="{{ route('profile.edit') }}"
                           class="group flex items-center gap-3 p-3.5 rounded-lg border border-slate-800/60
                                  hover:border-slate-700/80 hover:bg-slate-800/40 transition-all duration-150">
                            <div class="shrink-0 flex items-center justify-center w-9 h-9 rounded-lg
                                        bg-slate-800/60 border border-slate-700/40
                                        group-hover:bg-slate-700/60 transition-colors">
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-300 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-white">My Profile</p>
                                <p class="text-xs text-slate-500">Avatar, password, settings</p>
                            </div>
                        </a>

                        {{-- Encryption info (static) --}}
                        <div class="flex items-center gap-3 p-3.5 rounded-lg border border-slate-800/60 cursor-default">
                            <div class="shrink-0 flex items-center justify-center w-9 h-9 rounded-lg bg-violet-500/10 border border-violet-500/20">
                                <svg class="w-4 h-4 text-violet-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-white">Encryption</p>
                                <p class="text-xs text-violet-400 font-mono">AES-256-CBC per channel</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            {{-- ── Right Sidebar (1/3) ─────────────────────────────── --}}
            <div class="space-y-6">

                {{-- Security Protocol Stack --}}
                <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-3.5 h-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                        <h3 class="text-sm font-semibold text-white">Security Protocol</h3>
                    </div>

                    <div class="space-y-3">
                        @foreach([
                            ['label' => 'Encryption',    'value' => 'AES-256-CBC'],
                            ['label' => 'Steganography', 'value' => 'LSB — Blue channel'],
                            ['label' => 'Key Scope',     'value' => 'Per-conversation'],
                            ['label' => 'Image Format',  'value' => 'PNG lossless only'],
                        ] as $proto)
                            <div class="flex items-center justify-between py-2 border-b border-slate-800/60 last:border-0">
                                <span class="text-xs text-slate-400">{{ $proto['label'] }}</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span class="text-xs font-mono text-slate-300">{{ $proto['value'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pending Handshake Alert --}}
                @if($pendingIncoming->count() > 0)
                    <div class="bg-slate-900 border border-blue-500/25 rounded-xl p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                            </span>
                            <h3 class="text-sm font-semibold text-blue-300">Pending Handshakes</h3>
                        </div>

                        <div class="space-y-2">
                            @foreach($pendingIncoming->take(3) as $req)
                                <div class="flex items-center gap-2.5">
                                    <span class="shrink-0 flex items-center justify-center w-7 h-7 rounded-full bg-slate-800 border border-slate-700 text-slate-300 text-[10px] font-bold uppercase">
                                        {{ substr($req->sender->name, 0, 1) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium text-white truncate">{{ $req->sender->name }}</p>
                                        <p class="text-[10px] text-slate-500 truncate">Awaiting your accept</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <p class="text-[10px] text-slate-500 mt-3">
                            Open <span class="text-blue-400 font-medium">Requests</span> in the nav bar to respond.
                        </p>
                    </div>
                @endif

                {{-- Security Audit Trail --}}
                <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5 3 12l3.75 4.5m6.75 0 3.75-4.5L16.5 7.5M11.25 3v18" />
                            </svg>
                            <h3 class="text-sm font-semibold text-white">Security Audit Trail</h3>
                        </div>
                        <span class="flex items-center gap-1 text-[10px] font-mono text-emerald-400">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                            </span>
                            LIVE
                        </span>
                    </div>

                    <div class="bg-slate-950/60 border border-slate-800/60 rounded-lg p-3 space-y-1.5 font-mono text-[11px] leading-relaxed overflow-hidden">

                        @foreach($recentConnections as $conn)
                            @php
                                $partner = $conn->sender_id == $authId
                                    ? \App\Models\User::find($conn->recipient_id)
                                    : $conn->sender;
                            @endphp
                            @if($partner)
                                <div class="flex gap-2">
                                    <span class="text-slate-600 shrink-0">{{ $conn->updated_at->format('H:i:s') }}</span>
                                    <span>
                                        <span class="text-emerald-400">[HANDSHAKE]</span>
                                        <span class="text-slate-300"> Secure channel with </span>
                                        <span class="text-blue-400">{{ $partner->name }}</span>
                                        <span class="text-slate-400"> verified</span>
                                    </span>
                                </div>
                            @endif
                        @endforeach

                        <div class="flex gap-2">
                            <span class="text-slate-600 shrink-0">{{ now()->subMinutes(2)->format('H:i:s') }}</span>
                            <span>
                                <span class="text-blue-400">[AES-256]</span>
                                <span class="text-slate-300"> Conversation key derived — entropy OK</span>
                            </span>
                        </div>

                        @if($imagesTransmitted > 0)
                            <div class="flex gap-2">
                                <span class="text-slate-600 shrink-0">{{ now()->subMinutes(5)->format('H:i:s') }}</span>
                                <span>
                                    <span class="text-violet-400">[LSB-STEG]</span>
                                    <span class="text-slate-300"> PNG bit-stream verified — loss-free</span>
                                </span>
                            </div>
                        @endif

                        <div class="flex gap-2">
                            <span class="text-slate-600 shrink-0">{{ now()->subMinutes(8)->format('H:i:s') }}</span>
                            <span>
                                <span class="text-slate-400">[SYSTEM]</span>
                                <span class="text-slate-300"> Session authenticated as </span>
                                <span class="text-blue-400">{{ $user->name }}</span>
                            </span>
                        </div>

                        <div class="flex gap-2">
                            <span class="text-slate-600 shrink-0">{{ now()->subMinutes(12)->format('H:i:s') }}</span>
                            <span>
                                <span class="text-slate-400">[SYSTEM]</span>
                                <span class="text-slate-300"> Observer stack initialised — </span>
                                <span class="text-emerald-400">OK</span>
                            </span>
                        </div>

                        <div class="flex gap-2">
                            <span class="text-slate-600 shrink-0">{{ now()->subMinutes(15)->format('H:i:s') }}</span>
                            <span>
                                <span class="text-violet-400">[CRYPTO]</span>
                                <span class="text-slate-300"> AES-256-CBC encrypter ready</span>
                            </span>
                        </div>

                        <div class="flex gap-2 mt-0.5">
                            <span class="text-slate-600 shrink-0">{{ now()->format('H:i:s') }}</span>
                            <span class="text-emerald-400">
                                &gt; <span class="inline-block w-1.5 h-3 bg-emerald-400 align-middle animate-pulse ml-0.5"></span>
                            </span>
                        </div>

                    </div>
                </div>

            </div>
            {{-- ── end right sidebar ── --}}

        </div>
        {{-- ── end main grid ── --}}

    </div>
</div>
