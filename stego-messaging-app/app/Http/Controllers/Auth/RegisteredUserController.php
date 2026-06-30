<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // <-- Make sure this is imported at the top
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'avatar' => ['nullable', 'string'], // Handles your Base64 cropped data string cleanly
            'password' => [
                'required', 
                'confirmed', 
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);
    
        $avatarPath = null;
    
        // Check if the base64 image field payload data exists
        if ($request->filled('avatar')) {
            $base64String = $request->input('avatar');
            
            // Parse the base64 code string structure out safely
            $image_parts = explode(";base64,", $base64String);
            $image_base64 = base64_decode($image_parts[1]);
    
            // Generate a random path target string file key 
            $filename = 'avatars/' . Str::random(40) . '.jpg';
    
            // Direct binary transmission storage push into your Firebase cluster disk
            Storage::disk('firebase')->put($filename, $image_base64);
            $avatarPath = $filename;
        }
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'avatar' => $avatarPath,
            'password' => Hash::make($request->password),
        ]);
    
        event(new Registered($user));
        Auth::login($user);

        return redirect(route('verification.notice'));
    }
}