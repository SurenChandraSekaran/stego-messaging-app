<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class FriendsList extends Component
{
    public function removeFriend($friendId)
    {
        $authId = Auth::id();

        // Delete the friendship row regardless of who sent or received it
        DB::table('friendships')
            ->where(function($q) use ($authId, $friendId) {
                $q->where('sender_id', $authId)->where('recipient_id', $friendId);
            })
            ->orWhere(function($q) use ($authId, $friendId) {
                $q->where('sender_id', $friendId)->where('recipient_id', $authId);
            })
            ->delete();

        $this->dispatch('toast', [
            'title'   => 'Connection Severed',
            'message' => 'Friend has been removed from your network.',
            'type'    => 'info',
        ]);
    }

    public function render()
    {
        $authId = Auth::id();

        // Fetch all users who are accepted friends
        $friends = User::where('id', '!=', $authId)
            ->whereExists(function ($query) use ($authId) {
                $query->select(DB::raw(1))
                    ->from('friendships')
                    ->where('status', 'accepted')
                    ->where(function ($q) use ($authId) {
                        $q->whereColumn('friendships.sender_id', 'users.id')
                          ->where('friendships.recipient_id', $authId);
                    })
                    ->orWhere(function ($q) use ($authId) {
                        $q->whereColumn('friendships.recipient_id', 'users.id')
                          ->where('friendships.sender_id', $authId);
                    });
            })->get();

        return view('livewire.friends-list', [
            'friends' => $friends
        ]);
    }
}