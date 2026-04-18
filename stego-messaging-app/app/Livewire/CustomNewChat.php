<?php

namespace App\Livewire;

use Wirechat\Wirechat\Livewire\New\Chat as WirechatChat;
use Wirechat\Wirechat\Livewire\Concerns\HasPanel;
use Wirechat\Wirechat\Livewire\Concerns\Widget;
use App\Traits\HandlesFriendRequests;
use Livewire\Component;
use App\Models\User;

class CustomNewChat extends Component
{
    use HasPanel;
    use Widget;
    use HandlesFriendRequests;
    public $users = [];
    public $search;
    public static function modalAttributes(): array
    {
        return [
            'closeOnEscape' => true,
            'closeOnEscapeIsForceful' => true,
            'destroyOnClose' => true,
            'closeOnClickAway' => true,
            'maxWidth' => 'lg',
            'class' => 'sm:max-w-lg sm:rounded-lg',
            // You can add more attributes here if needed by the package
        ];
    }
    protected function getFriendIds()
    {
        $authId = auth()->id();

        // We keep this as a Collection (don't add ->toArray() at the end)
        return \App\Models\Friendship::where('status', 'accepted')
            ->where(function ($query) use ($authId) {
                $query->where('sender_id', $authId)
                    ->orWhere('recipient_id', $authId);
            })
            ->get()
            ->map(function ($f) use ($authId) {
                return $f->sender_id == $authId ? $f->recipient_id : $f->sender_id;
            })
            ->unique();
    }

    public function loadFriends()
    {
        $friendIds = $this->getFriendIds();

        // Now .isNotEmpty() will work because $friendIds is a Collection
        if ($friendIds->isNotEmpty()) {
            $this->users = \App\Models\User::whereIn('id', $friendIds)
                ->get()
                ->map(fn($user) => $this->formatForWirechat($user))
                ->toArray();
        } else {
            $this->users = [];
        }
    }
    public function updatedSearch()
    {
        if (blank($this->search)) {
            $this->loadFriends();
            return;
        }

        // Search everyone, but exclude yourself
        $this->users = User::where('id', '!=', auth()->id())
            ->where('name', 'like', '%' . $this->search . '%')
            ->limit(10)
            ->get()
            ->map(fn($user) => $this->formatForWirechat($user))
            ->toArray();
    }

/**
 * Helper to ensure the User model matches what wirechat::livewire.new.chat expects
 */
    protected function formatForWirechat($user)
    {
        return [
            'id' => $user->id,
            'wirechat_name' => $user->name, // Or $user->display_name if you have it
            'wirechat_avatar_url' => $user->avatar_url ?? null, // Match your avatar logic
            'type' => 'user', // Required for the createConversation method
        ];
    }
    public function mount()
    {
        // Ensure the user is authenticated, just like the original component
        abort_unless(auth()->check(), 401);
        $this->loadFriends();
    }
    public function render()
    {
        return view('wirechat::livewire.new.chat', [
            'users' => $this->users
        ]);
    }
}
