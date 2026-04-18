<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Friendship;
use App\Traits\HandlesFriendRequests;

class RequestInbox extends Component
{
    use HandlesFriendRequests;

    public function render()
    {
        $pendingRequests = Friendship::where('recipient_id', auth()->id())
            ->where('status', 'pending')
            ->with('sender')
            ->get();

        return view('livewire.request-inbox', [
            'requests' => $pendingRequests
        ]);
    }
}
