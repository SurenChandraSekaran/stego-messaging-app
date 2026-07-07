<?php

namespace App\Livewire;

use Wirechat\Wirechat\Livewire\Chat\Chat as WirechatChat;
use App\Services\SteganoService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class CustomChat extends WirechatChat
{
    public $scanCount = 0;
    const STEG_HEADER = '[STEG_SECURE]';
    public bool $steganoMode = false;

    public function toggleStegano()
    {
        $this->steganoMode = !$this->steganoMode;
    }

    public function sendMessage()
    {
        // ── STEP 1: GLOBAL SIZE VALIDATION FROM ADMIN PANEL ─────────────────────
        if (!empty($this->media)) {
            $maxMb = cache('max_payload_size', 25); // Dynamic admin setting (fallback to 25MB)
            $maxKb = $maxMb * 1024;                // Convert Megabytes to Kilobytes for Laravel

            try {
                // Validate all staging files in the media array
                $this->validate([
                    'media.*' => "max:{$maxKb}",
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                // Catch the failure, alert the user using your app's toast engine, and halt execution
                $this->dispatch('toast', [
                    'title' => 'File Too Large', 
                    'message' => "This file exceeds the system boundary of {$maxMb}MB set by the administrator.", 
                    'type' => 'error'
                ]);
                
                // Clear the media container so the bad file doesn't get stuck in the input loop
                $this->media = [];
                return;
            }
        }
        // ────────────────────────────────────────────────────────────────────────

        if (!$this->steganoMode) {
            parent::sendMessage();
            return;
        }
    
        if (empty($this->media)) {
            $this->dispatch('toast', ['title' => 'Error', 'message' => 'Please select an image.', 'type' => 'error']);
            return;
        }
    
        $secretText = $this->body; 
        if (empty($secretText)) {
            $this->dispatch('toast', ['title' => 'Error', 'message' => 'Please enter a secret message.', 'type' => 'error']);
            return;
        }
    
        // 1. Encrypt text payload
        $encryptedText = Crypt::encryptString(self::STEG_HEADER . $secretText);
        
        $uploadedFile = $this->media[0];
        $tempPath = $uploadedFile->getRealPath();
        
        $stegoFileName = 'stego_' . auth()->id() . '_' . time() . '.png';
        $localPath = storage_path('app/public/' . $stegoFileName);
    
        // 2. Embed payload into local PNG canvas
        app(SteganoService::class)->embed($tempPath, $encryptedText, $localPath);
    
        // 3. Keep extension as .png so WireChat reads the image file structures cleanly
        $pureFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $this->media[0] = new UploadedFile(
            $localPath, 
            $pureFilename . '.png', 
            'image/png', 
            null, 
            true
        );
    
        $this->body = null;
        $previousLatestId = $this->conversation->messages()->where('sendable_id', auth()->id())->latest()->value('id');
    
        // 4. Run WireChat's native upload pipeline
        parent::sendMessage();
    
        // 5. Catch generated database records
        $lastMessage = null;
        for ($i = 0; $i < 10; $i++) {
            $lastMessage = $this->conversation->messages()->where('sendable_id', auth()->id())->latest()->first();
            if ($lastMessage && ($previousLatestId === null || $lastMessage->id !== $previousLatestId)) {
                break;
            }
            usleep(50000); 
        }
    
        if ($lastMessage) {
            $attachment = \Wirechat\Wirechat\Models\Attachment::where('attachable_id', $lastMessage->id)->first();
            if ($attachment) {
                // 6. Direct overwrite on Cloud Storage with pristine local stego file
                Storage::disk('gcs')->put($attachment->file_path, fopen($localPath, 'r'));
    
                // 7. Stamp filename metadata prefix
                $attachment->update([
                    'file_name' => 'stego_' . $attachment->file_name
                ]);
            }
        }
    
        $this->steganoMode = false;

        // ── FIX: THE RENDER CYCLE SAFEGUARD ─────────────────────────────────────
        $this->media = [];
        // ────────────────────────────────────────────────────────────────────────
    
        if (file_exists($localPath)) {
            unlink($localPath);
        }
    }

    public function extractSecret($attachmentId)
    {
        try {
            $attachment = \Wirechat\Wirechat\Models\Attachment::findOrFail($attachmentId);
            
            if (!str_starts_with($attachment->file_name ?? '', 'stego_')) {
                $this->dispatch('toast', [
                    'title'   => 'Scan Complete',
                    'message' => 'No hidden payload detected.',
                    'type'    => 'info',
                ]);
                return;
            }
    
            $content = Storage::disk('gcs')->get($attachment->file_path);
            $tempPath = storage_path('app/public/temp_extract_' . auth()->id() . '_' . time() . '.png');
            file_put_contents($tempPath, $content);
    
            $rawBits = app(SteganoService::class)->extract($tempPath);
    
            $decrypted = null;
            if ($rawBits) {
                try {
                    $decrypted = Crypt::decryptString($rawBits);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $decrypted = null;
                }
            }
    
            if ($decrypted && str_starts_with($decrypted, self::STEG_HEADER)) {
                $this->dispatch('reveal-payload', [
                    'message' => substr($decrypted, strlen(self::STEG_HEADER))
                ]);
                return;
            }
    
            $this->dispatch('toast', [
                'title'   => 'Scan Complete',
                'message' => 'No hidden payload detected.',
                'type'    => 'info',
            ]);
    
        } catch (\Throwable $e) {
            \Log::error('[StegExtract] Failed: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title'   => 'Extraction Error',
                'message' => 'An unexpected error occurred while analyzing the image.',
                'type'    => 'error',
            ]);
        } finally {
            if (isset($tempPath) && file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }

    public function render()
    {
        return view('wirechat::livewire.chat.chat');
    }
}