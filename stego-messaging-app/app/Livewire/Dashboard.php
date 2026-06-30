<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Friendship;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $user      = Auth::user();
        $authId    = $user->id;
        $morphType = $user->getMorphClass();

        $pendingIncoming = Friendship::where('recipient_id', $authId)
            ->where('status', 'pending')
            ->with('sender')
            ->get();

        $imagesTransmitted = 0;
        $unreadCount       = 0;
        try {
            $imagesTransmitted = \Wirechat\Wirechat\Models\Message
                ::where('sendable_id', $authId)
                ->where('sendable_type', $morphType)
                ->where('type', \Wirechat\Wirechat\Enums\MessageType::ATTACHMENT)
                ->count();
            $unreadCount = $user->getUnreadCount();
        } catch (\Throwable $e) {}

        $recentConnections = Friendship::where('status', 'accepted')
            ->where(fn ($q) => $q->where('sender_id', $authId)->orWhere('recipient_id', $authId))
            ->latest('updated_at')
            ->limit(2)
            ->get();

        return view('livewire.dashboard-page', compact(
            'user',
            'authId',
            'pendingIncoming',
            'imagesTransmitted',
            'unreadCount',
            'recentConnections',
        ))->layout('layouts.app');
    }
}
