@use('Wirechat\Wirechat\Facades\Wirechat')
<div id="new-chat-modal">
    <div class="relative w-full h-96 border mx-auto border-slate-800 overflow-auto bg-slate-950 text-white px-7 sm:max-w-lg sm:rounded-xl shadow-2xl">

        <header class="sticky top-0 bg-slate-950 z-10 py-2">
            <div class="flex justify-between items-center pb-2">
                <h3 class="text-sm font-semibold tracking-widest text-slate-300 uppercase font-mono">
                    {{ __('wirechat::new.chat.labels.heading') }}
                </h3>

                <x-wirechat::actions.close-modal>
                    <button dusk="close_modal_button"
                        class="p-1.5 text-slate-400 hover:bg-slate-800 hover:text-slate-200 rounded-lg transition-colors">
                        <svg class="w-4 h-4 cursor-pointer" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </x-wirechat::actions.close-modal>
            </div>

            <section class="flex flex-wrap items-center px-0 border-b border-slate-800">
                <input dusk="search_users_field" autofocus type="search" id="users-search-field"
                    wire:model.live.debounce='search' autocomplete="off"
                    placeholder="{{ __('wirechat::new.chat.inputs.search.placeholder') }}"
                    class="wc-input w-full border-0 py-2 px-0 bg-slate-950 text-white placeholder-slate-600 outline-hidden focus:outline-hidden rounded-lg focus:ring-0 hover:ring-0 text-sm">
            </section>
        </header>

        <div class="relative w-full">

            <section class="my-4 grid">
                @if (count($users) != 0)
                    <ul class="overflow-auto flex flex-col gap-0.5">
                        @foreach ($users as $key => $user)
                            @php
                                $friendship = \App\Models\Friendship::where(function($q) use ($user) {
                                    $q->where('sender_id', auth()->id())->where('recipient_id', $user['id']);
                                })->orWhere(function($q) use ($user) {
                                    $q->where('sender_id', $user['id'])->where('recipient_id', auth()->id());
                                })->first();
                            @endphp

                            <li wire:key="user-{{ $key }}"
                                @if($friendship && $friendship->status === 'accepted')
                                wire:click="createConversation('{{ $user['id'] }}')"
                                @elseif(!$friendship)
                                    wire:click="sendChatRequest('{{ $user['id'] }}')"
                                @endif

                                class="flex gap-3 items-center p-2.5 rounded-lg transition-colors
                                @if($friendship && $friendship->status === 'pending')
                                    cursor-default opacity-60
                                @else
                                    cursor-pointer hover:bg-slate-800/60
                                @endif"
                            >
                                <x-wirechat::avatar :src="$user['wirechat_avatar_url']" class="w-9 h-9 shrink-0" />

                                <div class="flex flex-col flex-1 min-w-0">
                                    <p class="font-medium text-slate-100 text-sm truncate">
                                        {{ $user['wirechat_name'] }}
                                    </p>

                                    @if(!$friendship)
                                        <span class="text-[11px] text-blue-400 font-mono tracking-wide">[ INITIATE HANDSHAKE ]</span>
                                    @elseif($friendship->status === 'pending')
                                        <span class="text-[11px] text-amber-400 font-mono flex items-center gap-1">
                                            <svg class="w-2.5 h-2.5 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg>
                                            HANDSHAKE PENDING
                                        </span>
                                    @elseif($friendship->status === 'accepted')
                                        <span class="text-[11px] text-emerald-400 font-mono tracking-wide">&#x2713; SECURE CHANNEL AVAILABLE</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    @if (!empty($search))
                        <span class="text-slate-500 text-sm font-mono text-center py-4">@lang('wirechat::new.chat.messages.empty_search_result')</span>
                    @endif
                @endif
            </section>
        </div>
    </div>
</div>
