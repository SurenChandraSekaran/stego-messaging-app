<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Livewire\CustomChat;

class StegoController extends Controller
{
    public function extract(Request $request)
    {
        // Direct call to your existing logic
        // We instantiate the class to use the method
        $chat = new CustomChat();
        return $chat->extractSecret($request->id);
    }
}