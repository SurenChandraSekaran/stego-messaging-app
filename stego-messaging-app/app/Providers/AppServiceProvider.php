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

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('wirechat.new.chat', \App\Livewire\CustomNewChat::class);
    }
}
