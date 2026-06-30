<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UpdateAvatarForm extends Component
{
    public $avatar; // Will capture the Base64 string from the frontend handler layout

    protected $rules = [
        'avatar' => 'required|string', // Validated as a valid string blob pattern transfer
    ];

    public function saveAvatar()
    {
        $this->validate();

        $user = Auth::user();

        try {
            // Clean out the old user image reference from Firebase storage buckets
            if ($user->avatar && Storage::disk('firebase')->exists($user->avatar)) {
                Storage::disk('firebase')->delete($user->avatar);
            }

            // Parse the incoming base64 image string structure
            // Example data string: "data:image/jpeg;base64,/9j/4AAQSkZJRg..."
            $image_parts = explode(";base64,", $this->avatar);
            $image_base64 = base64_decode($image_parts[1]);

            // Generate a random, unique cryptographic tracking name for the asset
            $filename = 'avatars/' . Str::random(40) . '.jpg';

            // Upload the raw decoded binary image directly to your firebase storage disk
            Storage::disk('firebase')->put($filename, $image_base64);

            // Sync structural database attributes
            $user->update([
                'avatar' => $filename
            ]);

            $this->reset('avatar');

            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => 'Profile picture cropped and updated successfully.'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Failed to save cropped image: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.update-avatar-form');
    }
}