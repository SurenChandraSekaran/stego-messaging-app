<?php

namespace App\Livewire;

use Livewire\Component;
use App\Traits\HandlesFriendRequests;
use App\Models\Friendship;

class FriendRequests extends Component
{
    use HandlesFriendRequests; // This gives you access to acceptRequest()

    public function render()
    {
        $requests = Friendship::where('recipient_id', auth()->id())
            ->where('status', 'pending')
            ->with('sender') // Make sure your Friendship model has a 'sender' relationship
            ->get();

        return view('livewire.friend-requests', ['requests' => $requests]);
    }
}
