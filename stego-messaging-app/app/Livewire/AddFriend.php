<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Friendship;
use App\Traits\HandlesFriendRequests;
use Illuminate\Support\Facades\Auth;

class AddFriend extends Component
{
    use HandlesFriendRequests;

    public string $search = '';
    public array $users = [];

    public function updatedSearch(): void
    {
        if (strlen(trim($this->search)) < 2) {
            $this->users = [];
            return;
        }

        $authId = Auth::id();

        // Accepted friends are hidden entirely (they belong in "My Friends").
        $acceptedIds = Friendship::where('status', 'accepted')
            ->where(function ($q) use ($authId) {
                $q->where('sender_id', $authId)->orWhere('recipient_id', $authId);
            })
            ->get()
            ->map(fn ($f) => $f->sender_id == $authId ? $f->recipient_id : $f->sender_id)
            ->collect()
            ->push($authId)
            ->unique()
            ->toArray();

        // Pending requests *sent by* the current user — shown with a "Pending" badge.
        $pendingIds = Friendship::where('sender_id', $authId)
            ->where('status', 'pending')
            ->pluck('recipient_id')
            ->toArray();

        $users = User::where('name', 'like', '%' . trim($this->search) . '%')
            ->whereNotIn('id', $acceptedIds)
            ->limit(8)
            ->get(['id', 'name', 'email'])
            ->toArray();

        $this->users = collect($users)->map(function ($user) use ($pendingIds) {
            $user['status'] = in_array($user['id'], $pendingIds) ? 'pending' : 'none';
            return $user;
        })->toArray();
    }

    /**
     * Overrides the trait method so we can refresh the result list immediately
     * after sending, giving instant "Handshake Pending" UI feedback.
     */
    public function sendChatRequest($userId): void
    {
        $senderId = Auth::id();

        $exists = Friendship::where(function ($q) use ($senderId, $userId) {
            $q->where('sender_id', $senderId)->where('recipient_id', $userId);
        })->orWhere(function ($q) use ($senderId, $userId) {
            $q->where('sender_id', $userId)->where('recipient_id', $senderId);
        })->exists();

        if (! $exists) {
            Friendship::create([
                'sender_id'    => $senderId,
                'recipient_id' => $userId,
                'status'       => 'pending',
            ]);
            $this->dispatch('toast', ['title' => 'Handshake Initiated', 'message' => 'Friend request sent successfully.', 'type' => 'success']);
        } else {
            $this->dispatch('toast', ['title' => 'Already Sent', 'message' => 'A handshake request already exists.', 'type' => 'info']);
        }

        $this->updatedSearch();
    }

    public function cancelRequest(int $userId): void
    {
        Friendship::where('sender_id', Auth::id())
            ->where('recipient_id', $userId)
            ->where('status', 'pending')
            ->delete();

        $this->dispatch('toast', ['title' => 'Request Cancelled', 'message' => 'Pending handshake removed.', 'type' => 'info']);
        $this->updatedSearch();
    }

    public function render()
    {
        return view('livewire.add-friend');
    }
}
