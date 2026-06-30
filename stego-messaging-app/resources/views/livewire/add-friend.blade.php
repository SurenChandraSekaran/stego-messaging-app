<div>
    {{-- Search input --}}
    <div class="p-3 border-b border-slate-800">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-500 pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
            </svg>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search by name…"
                autocomplete="off"
                class="w-full bg-slate-800/60 border border-slate-700/60 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500/60 focus:border-blue-500/60 transition"
            />
        </div>
    </div>

    {{-- Results list --}}
    <div class="max-h-60 overflow-y-auto">
        @if(strlen(trim($search)) < 2)
            <p class="px-4 py-8 text-center text-xs text-slate-500">
                Type at least 2 characters to search for users.
            </p>
        @elseif(empty($users))
            <p class="px-4 py-8 text-center text-xs text-slate-500">
                No users found matching "{{ $search }}".
            </p>
        @else
            <div class="divide-y divide-slate-800/60 py-1">
                @foreach($users as $user)
                    <div class="flex items-center justify-between px-4 py-2.5 hover:bg-slate-800/40 transition-colors duration-100">

                        {{-- Avatar + name --}}
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-slate-700/60 border border-slate-600/40 text-slate-300 text-xs font-bold uppercase">
                                {{ substr($user['name'], 0, 1) }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-white truncate">{{ $user['name'] }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $user['email'] }}</p>
                            </div>
                        </div>

                        {{-- Action area --}}
                        <div class="shrink-0 ml-3 flex items-center gap-1.5">
                            @if($user['status'] === 'pending')
                                {{-- Pending state badge --}}
                                <span class="text-xs font-medium text-amber-400 bg-amber-400/10 border border-amber-400/20 px-2.5 py-1 rounded-lg whitespace-nowrap">
                                    Handshake Sent
                                </span>

                                {{-- Cancel request --}}
                                <button
                                    wire:click="cancelRequest({{ $user['id'] }})"
                                    wire:loading.attr="disabled"
                                    wire:target="cancelRequest({{ $user['id'] }})"
                                    class="text-xs font-semibold px-2.5 py-1 rounded-lg
                                           bg-rose-600/10 text-rose-400 border border-rose-500/20
                                           hover:bg-rose-600/25 hover:border-rose-400/50 hover:text-rose-300
                                           disabled:opacity-40 disabled:cursor-not-allowed
                                           transition-all duration-150"
                                >
                                    <span wire:loading.remove wire:target="cancelRequest({{ $user['id'] }})">Cancel</span>
                                    <span wire:loading wire:target="cancelRequest({{ $user['id'] }})">…</span>
                                </button>
                            @else
                                {{-- Send request --}}
                                <button
                                    wire:click="sendChatRequest({{ $user['id'] }})"
                                    wire:loading.attr="disabled"
                                    wire:target="sendChatRequest({{ $user['id'] }})"
                                    class="text-xs font-semibold px-3 py-1.5 rounded-lg
                                           bg-blue-600/10 text-blue-400 border border-blue-500/25
                                           hover:bg-blue-600/25 hover:border-blue-400/50 hover:text-blue-300
                                           disabled:opacity-40 disabled:cursor-not-allowed
                                           transition-all duration-150"
                                >
                                    <span wire:loading.remove wire:target="sendChatRequest({{ $user['id'] }})">Add</span>
                                    <span wire:loading wire:target="sendChatRequest({{ $user['id'] }})">…</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
