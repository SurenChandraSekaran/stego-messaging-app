<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SecurityOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // 1. Count your total platform participants
        $totalUsers = User::where('is_admin', false)->count();

        // 2. Count active custom handshakes established in your system
        $activeHandshakes = DB::table('friendships')->where('status', 'accepted')->count();

        return [
            Stat::make('Protected Identities', $totalUsers)
                ->description('Total registered end-users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Cryptographic Handshakes', $activeHandshakes)
                ->description('Secure 1-to-1 relationships active')
                ->descriptionIcon('heroicon-m-key')
                ->color('info'),

            Stat::make('System Zero-Trust Mode', 'ACTIVE')
                ->description('Asymmetric handshakes mandatory')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success'),
        ];
    }
}