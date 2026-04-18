<div>
    <x-dropdown align="left" width="64"> {{-- Changed align to left since it's on the left side now --}}
        <x-slot name="trigger">
            <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none transition duration-150 ease-in-out relative">
                {{ __('Requests') }}
                
                @if($requests->count() > 0)
                    <span class="ms-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                        {{ $requests->count() }}
                    </span>
                @endif
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="block px-4 py-2 text-xs text-gray-400 uppercase tracking-widest">
                {{ __('Pending Requests') }}
            </div>
            <div class="max-h-64 overflow-y-auto">
                @forelse($requests as $request)
                    <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <span class="text-sm font-semibold">{{ $request->sender->name }}</span>
                        <button wire:click="acceptRequest({{ $request->sender_id }})" class="bg-blue-600 text-white text-xs px-2 py-1 rounded-md">
                            Accept
                        </button>
                    </div>
                @empty
                    <div class="p-4 text-center text-sm text-gray-500">No new requests</div>
                @endforelse
            </div>
        </x-slot>
    </x-dropdown>
</div>