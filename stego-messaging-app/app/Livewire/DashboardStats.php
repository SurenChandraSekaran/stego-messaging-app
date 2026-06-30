<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Friendship;
use Illuminate\Support\Facades\Auth;

class DashboardStats extends Component
{
    public function render()
    {
        $user      = Auth::user();
        $userId    = $user->id;
        $morphType = $user->getMorphClass();

        $secureChannels = Friendship::where('status', 'accepted')
            ->where(fn ($q) => $q->where('sender_id', $userId)->orWhere('recipient_id', $userId))
            ->count();

        $pendingCount = Friendship::where('recipient_id', $userId)
            ->where('status', 'pending')
            ->count();

        $imagesTransmitted = 0;
        $unreadCount       = 0;
        try {
            $imagesTransmitted = \Wirechat\Wirechat\Models\Message
                ::where('sendable_id', $userId)
                ->where('sendable_type', $morphType)
                ->where('type', \Wirechat\Wirechat\Enums\MessageType::ATTACHMENT)
                ->count();
            $unreadCount = $user->getUnreadCount();
        } catch (\Throwable $e) {}

        return view('livewire.dashboard-stats', compact(
            'secureChannels',
            'imagesTransmitted',
            'unreadCount',
            'pendingCount',
        ));
    }
}
