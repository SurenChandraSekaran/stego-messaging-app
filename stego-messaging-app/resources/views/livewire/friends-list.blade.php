<div class="bg-slate-900 border border-slate-800/60 p-6 rounded-xl shadow-sm" wire:poll.10s>
    <header class="mb-5">
        <h2 class="text-lg font-semibold text-white">My Friends</h2>
        <p class="mt-1 text-sm text-slate-400">Manage your active connections. Removing a friend disables secure chat handshakes.</p>
    </header>

    @if($friends->isEmpty())
        <div class="text-center py-8 text-slate-500 text-sm">
            You haven't added any friends yet.
        </div>
    @else
        <div class="divide-y divide-slate-800">
            @foreach($friends as $friend)
                <div class="flex items-center justify-between py-3.5 first:pt-0 last:pb-0">
                    <div class="flex items-center gap-3">
                        <img src="{{ $friend->avatar_url }}" class="w-10 h-10 rounded-full object-cover border border-slate-700">
                        
                        <div>
                            <h4 class="text-sm font-medium text-white">{{ $friend->name }}</h4>
                            <p class="text-xs text-slate-400">{{ $friend->email }}</p>
                        </div>
                    </div>

                    <div x-data="{ confirming: false }">
                        <button
                            type="button"
                            x-show="!confirming"
                            @click="confirming = true"
                            class="text-xs bg-rose-950/40 text-rose-400 hover:bg-rose-900/60 font-semibold py-1.5 px-3 rounded-lg border border-rose-800/50 transition duration-150"
                        >Remove</button>
                        <div x-show="confirming" class="flex items-center gap-1.5">
                            <span class="text-[11px] text-slate-400 font-mono">Sure?</span>
                            <button
                                type="button"
                                wire:click="removeFriend({{ $friend->id }})"
                                @click="confirming = false"
                                class="text-[11px] font-semibold px-2.5 py-1 rounded-md bg-rose-600/80 hover:bg-rose-600 text-white transition-colors"
                            >Yes</button>
                            <button
                                type="button"
                                @click="confirming = false"
                                class="text-[11px] font-semibold px-2.5 py-1 rounded-md bg-slate-700 hover:bg-slate-600 text-slate-300 transition-colors"
                            >No</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>