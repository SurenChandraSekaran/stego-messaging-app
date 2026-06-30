<?php

namespace App\Observers;

use App\Services\ChatSecurityService;
use Wirechat\Wirechat\Models\Conversation;

class ConversationObserver
{
    /**
     * Handle the Conversation "creating" event.
     */
    public function creating(Conversation $conversation): void
    {
        $security = new ChatSecurityService();
        // Generate and assign the unique key before the record hits the database
        $conversation->security_key = $security->generateKey();
    }
}