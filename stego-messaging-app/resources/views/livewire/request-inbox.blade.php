<div x-data="{ open: false }" @click.away="open = false" class="relative" wire:poll.8s>

    {{-- ── Trigger pill ── --}}
    <button
        @click="open = !open"
        :class="open ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'"
        class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-150 focus:outline-none"
    >
        {{-- Bell icon --}}
        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>

        Requests

        {{-- Live count badge — only shown when there are pending items --}}
        @if($requests->count() > 0)
            <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full">
                {{ $requests->count() }}
            </span>
        @endif

        {{-- Chevron --}}
        <svg class="w-3 h-3 transition-transform duration-150" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    {{-- ── Dropdown panel ── --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute top-full left-0 mt-2 w-80 bg-slate-900 border border-slate-800/80 rounded-xl shadow-2xl shadow-black/40 overflow-hidden z-50"
        style="display: none;"
    >
        {{-- Panel header --}}
        <div class="px-4 py-2.5 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
                <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Incoming Requests</span>
            </div>

            @if($requests->count() > 0)
                <span class="text-xs font-bold text-emerald-400 bg-emerald-400/10 border border-emerald-400/20 px-2 py-0.5 rounded-full">
                    {{ $requests->count() }} pending
                </span>
            @endif
        </div>

        {{-- Request list --}}
        <div class="max-h-72 overflow-y-auto">
            @forelse($requests as $request)
                <div
                    wire:key="request-{{ $request->id }}"
                    class="flex items-center justify-between px-4 py-2.5 hover:bg-slate-800/40 transition-colors duration-100"
                >
                    {{-- Sender avatar + name --}}
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-slate-700/60 border border-slate-600/40 text-slate-300 text-xs font-bold uppercase">
                            {{ substr($request->sender->name, 0, 1) }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ $request->sender->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $request->sender->email }}</p>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="shrink-0 ml-3 flex items-center gap-1.5">

                        {{-- Accept — emerald accent (wire:click binding preserved) --}}
                        <button
                            wire:click="acceptRequest({{ $request->sender_id }})"
                            wire:loading.attr="disabled"
                            wire:target="acceptRequest({{ $request->sender_id }})"
                            class="text-xs font-semibold px-2.5 py-1 rounded-lg
                                   bg-emerald-600/10 text-emerald-400 border border-emerald-500/25
                                   hover:bg-emerald-600/25 hover:border-emerald-400/50 hover:text-emerald-300
                                   disabled:opacity-40 disabled:cursor-not-allowed
                                   transition-all duration-150"
                        >
                            <span wire:loading.remove wire:target="acceptRequest({{ $request->sender_id }})">Accept</span>
                            <span wire:loading wire:target="acceptRequest({{ $request->sender_id }})" class="inline-flex items-center gap-1">
                                <svg class="w-3 h-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </span>
                        </button>

                        {{-- Decline — muted slate border --}}
                        <button
                            wire:click="declineRequest({{ $request->id }})"
                            wire:loading.attr="disabled"
                            wire:target="declineRequest({{ $request->id }})"
                            class="text-xs font-semibold px-2.5 py-1 rounded-lg
                                   bg-slate-700/30 text-slate-400 border border-slate-600/30
                                   hover:bg-slate-700/60 hover:text-slate-200 hover:border-slate-500/50
                                   disabled:opacity-40 disabled:cursor-not-allowed
                                   transition-all duration-150"
                        >
                            <span wire:loading.remove wire:target="declineRequest({{ $request->id }})">Decline</span>
                            <span wire:loading wire:target="declineRequest({{ $request->id }})" class="inline-flex items-center gap-1">
                                <svg class="w-3 h-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            @empty
                {{-- Empty state --}}
                <div class="px-4 py-10 flex flex-col items-center gap-2">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-800/60 border border-slate-700/40">
                        <svg class="w-4.5 h-4.5 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </div>
                    <p class="text-xs text-slate-500">No pending requests</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
