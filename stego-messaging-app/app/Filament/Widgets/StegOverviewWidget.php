<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StegOverviewWidget extends BaseWidget
{
    // Auto-refresh the metrics on the admin panel every 10 seconds
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        // 1. Calculate Total Steganographic Images Sent
        // Queries Wirechat's attachments table looking for your custom native prefix
        $totalStegoMessages = DB::table('wirechat_attachments')
            ->where('file_name', 'like', 'stego_%')
            ->count();

        // 2. Calculate the current configuration payload limits
        $maxPayloadSize = cache('max_payload_size', 25);

        return [
            Stat::make('Stego Transmissions', $totalStegoMessages)
                ->description('Total verified LSB hidden payloads')
                ->descriptionIcon('heroicon-m-arrow-up-tray')
                ->color('emerald'),

            Stat::make('Active Carrier Guard', $maxPayloadSize . ' MB')
                ->description('Maximum file size allowed for injections')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($maxPayloadSize > 50 ? 'warning' : 'success'),
        ];
    }
}