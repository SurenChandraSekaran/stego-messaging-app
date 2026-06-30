<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Wirechat\Wirechat\Contracts\WirechatUser;
use Wirechat\Wirechat\Panel as WirechatPanel; // Alias this so it doesn't conflict with Filament's Panel
use Wirechat\Wirechat\Traits\InteractsWithWirechat;
use Illuminate\Support\Facades\Storage;

// 1. ADD THIS IMPORT FOR THE FILAMENT CORE GATE
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel as FilamentPanel;

// 2. ADD 'implements FilamentUser' TO YOUR CLASS DEFINITION LINE
class User extends Authenticatable implements WirechatUser, FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable, InteractsWithWirechat;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar', 
        'is_admin', // Ensure this is fillable for dashboard controls!
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        //'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * 3. THE ADMIN GATEWAY RULE
     * Determines who is authorized to step into your admin dashboard context.
     */
    public function canAccessPanel(FilamentPanel $panel): bool
    {
        // Only users with an explicit admin flag set in the database can authenticate here
        return $this->is_admin === true;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Keep your existing Wirechat panel methods exactly as they are below...
    // ─────────────────────────────────────────────────────────────────────────

    public function canAccessWirechatPanel(WirechatPanel $panel): bool
    {
        return true;
    }

    public function canCreateChats(): bool
    {
        return true;
    }

    public function canCreateGroups(): bool
    {
        return true;
    }

    public function wirechatSearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public function isFriendsWith(User $user): bool
    {
        return \DB::table('friendships')
            ->where(function($q) use ($user) {
                $q->where('sender_id', $this->id)->where('recipient_id', $user->id);
            })->orWhere(function($q) use ($user) {
                $q->where('sender_id', $user->id)->where('recipient_id', $this->id);
            })->where('status', 'accepted')->exists();
    }

    public function canSendMessageTo(User $receiver): bool
    {
        return $this->isFriendsWith($receiver);
    }

    public function getAvatarUrlAttribute(): string
    {
        if (!$this->avatar) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1e293b&color=fff';
        }

        $bucket = config('filesystems.disks.firebase.bucket');
        return "https://firebasestorage.googleapis.com/v0/b/{$bucket}/o/" . urlencode($this->avatar) . "?alt=media";
    }

    public function getCoverUrl(): ?string
    {
        return $this->getAvatarUrlAttribute();
    }

    public function getWirechatAvatarUrlAttribute(): ?string
    {
        return $this->getAvatarUrlAttribute();
    }
    protected static function booted(): void
    {
        static::creating(function ($user) {
            // If the account is being created via the terminal command line
            if (app()->runningInConsole()) {
                $user->is_admin = true;
                $user->email_verified_at = now(); // Automatically verifies them too!
            }
        });
    }
}