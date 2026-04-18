@foreach($requests as $request)
    <div>
        <span>{{ $request->sender->name }} wants to chat!</span>
        <button wire:click="acceptRequest({{ $request->sender_id }})" class="bg-green-500 text-white px-4 py-2">
            Accept
        </button>
    </div>
@endforeach