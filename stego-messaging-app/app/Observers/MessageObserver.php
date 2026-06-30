<?php

namespace App\Observers;

use App\Models\User;
use App\Services\ChatSecurityService;
use Wirechat\Wirechat\Enums\ConversationType;
use Wirechat\Wirechat\Models\Message;

class MessageObserver
{
    protected $security;

    public function __construct(ChatSecurityService $security)
    {
        $this->security = $security;
    }

    public function creating(Message $message)
    {
        $conversation = $message->conversation;

        // Block message delivery in private 1-to-1 chats when friendship is revoked.
        // Group and self conversations are not subject to the friendship gate.
        if ($conversation && $conversation->type === ConversationType::PRIVATE) {
            $sender = User::find($message->sendable_id);

            $receiverParticipant = $conversation->participants()
                ->where('participantable_id', '!=', $message->sendable_id)
                ->first();

            if ($sender && $receiverParticipant) {
                $receiver = User::find($receiverParticipant->participantable_id);

                if ($receiver && ! $sender->canSendMessageTo($receiver)) {
                    abort(403, 'Friendship required. Remove the contact and reconnect to resume messaging.');
                }
            }
        }

        // Encrypt body with the conversation's shared security key before persisting.
        $key = $conversation->security_key ?? null;
        if ($message->body && $key) {
            $message->body = $this->security->encrypt($message->body, $key);
        }
    }
}
