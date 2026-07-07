<?php

namespace App\Providers\Wirechat;

use Wirechat\Wirechat\Panel;
use Wirechat\Wirechat\PanelProvider;

class ChatsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
             ->id('chats')
             ->path('chats')
             ->middleware(['web', 'auth'])
             ->default()
             ->heading('StegChat')
             ->chatsSearch()
             ->mediaMaxUploadSize(256000) // Changes Media limit to 250MB
             ->fileMaxUploadSize(256000)  // Changes File limit to 250MB
             ->createChatAction()
             ->createGroupAction()
             ->emojiPicker()
             ->attachments()
             ->messagesQueue('messages')
             ->eventsQueue('default')
             ->layout('layouts.app')
             //->redirectToHomeAction()
             ->homeUrl('/dashboard');
    }
}