<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Storage\StorageClient;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use League\Flysystem\Filesystem;
use Livewire\Livewire;
use Wirechat\Wirechat\Livewire\New\Chat as WirechatChat;
use App\Traits\HandlesFriendRequests;
use Wirechat\Wirechat\Models\Conversation; 
use App\Observers\ConversationObserver;
use Wirechat\Wirechat\Models\Message;
use App\Observers\MessageObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\SteganoService::class, function ($app) {
            return new \App\Services\SteganoService();
        });
    }
    

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Try both layout naming conventions to ensure the hijack succeeds
        Livewire::component('wirechat.chat', \App\Livewire\CustomChat::class);
        Livewire::component('wirechat.chat.chat', \App\Livewire\CustomChat::class);
        Livewire::component('wirechat.new.chat', \App\Livewire\CustomNewChat::class);

        Message::observe(MessageObserver::class);
        Conversation::observe(ConversationObserver::class);
    }
} 


