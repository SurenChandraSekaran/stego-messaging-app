<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Wirechat\Wirechat\Contracts\WirechatUser;
use Wirechat\Wirechat\Panel;
use Wirechat\Wirechat\Traits\InteractsWithWirechat;

class User extends Authenticatable implements WirechatUser
{
    use HasFactory, Notifiable, InteractsWithWirechat;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessWirechatPanel(Panel $panel): bool
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
}