<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Support\Facades\Auth;
use Wirechat\Wirechat\Facades\Wirechat;
use Illuminate\Support\Facades\DB;


trait HandlesFriendRequests
{
    public function sendChatRequest($userId)
    {
        $senderId = auth()->id();

        $exists = Friendship::where(function($q) use ($senderId, $userId) {
                $q->where('sender_id', $senderId)->where('recipient_id', $userId);
            })->orWhere(function($q) use ($senderId, $userId) {
                $q->where('sender_id', $userId)->where('recipient_id', $senderId);
            })->exists();

        if (!$exists) {
            Friendship::create([
                'sender_id' => $senderId,
                'recipient_id' => $userId,
                'status' => 'pending',
            ]);
            
            $this->dispatch('notify', ['message' => 'Request sent!', 'type' => 'success']);
        } else {
            $this->dispatch('notify', ['message' => 'Request already exists.', 'type' => 'info']);
        }
    }

    public function acceptRequest($senderId)
    {
        $sender = \App\Models\User::find($senderId);
        $user = auth()->user();
        
        $friendship = \App\Models\Friendship::where('sender_id', $senderId)
            ->where('recipient_id', $user->id)
            ->where('status', 'pending')
            ->first();
    
        if ($friendship && $sender) {
            $friendship->update(['status' => 'accepted']);
            $conversation = $user->createConversationWith($sender);
    
            $this->dispatch('notify', ['message' => 'Handshake accepted!']);
            
            // Use direct path to avoid route naming issues
            return redirect()->to('/chats/' . $conversation->id);
        }
    }
    
    public function createConversation($id, string $class = null)
    {
        $recipient = \App\Models\User::find($id);
        $user = auth()->user();
    
        $friendship = \App\Models\Friendship::where(function($q) use ($user, $id) {
                $q->where('sender_id', $user->id)->where('recipient_id', $id);
            })->orWhere(function($q) use ($user, $id) {
                $q->where('sender_id', $id)->where('recipient_id', $user->id);
            })
            ->where('status', 'accepted')
            ->first();
    
        if ($friendship) {
            $conversation = $user->createConversationWith($recipient);
            
            if (method_exists($this, 'closeWirechatModal')) {
                $this->closeWirechatModal();
            }
    
            // Use direct path
            return redirect()->to('/chats/' . $conversation->id);
        }
    
        $this->dispatch('notify', ['message' => 'Handshake required.', 'type' => 'error']);
    }
}