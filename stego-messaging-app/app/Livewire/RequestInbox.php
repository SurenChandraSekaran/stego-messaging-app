<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Friendship;
use App\Traits\HandlesFriendRequests;

class RequestInbox extends Component
{
    use HandlesFriendRequests;

    /**
     * Decline an incoming pending request by its primary key.
     * Guarded so only the intended recipient can delete the row.
     */
    public function declineRequest(int $friendshipId): void
    {
        Friendship::where('id', $friendshipId)
            ->where('recipient_id', auth()->id())
            ->where('status', 'pending')
            ->delete();

        $this->dispatch('toast', ['title' => 'Handshake Declined', 'message' => 'Request declined.', 'type' => 'info']);
    }

    public function render()
    {
        $pendingRequests = Friendship::where('recipient_id', auth()->id())
            ->where('status', 'pending')
            ->with('sender')
            ->get();

        return view('livewire.request-inbox', [
            'requests' => $pendingRequests,
        ]);
    }
}
