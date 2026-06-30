<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class SystemSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-m-cog-6-tooth';
    protected static string $view = 'filament.pages.system-settings';
    protected static ?string $navigationGroup = 'System Controls';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'max_payload_size' => cache('max_payload_size', 25),
            'system_notice'    => cache('system_notice', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Carrier File Constraints')
                    ->description('Set the maximum size (in MB) allowed for image carrier uploads.')
                    ->schema([
                        TextInput::make('max_payload_size')
                            ->label('Max Carrier File Size (MB)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(100),
                    ]),

                Section::make('Global Frontend Notice')
                    ->description('Broadcast an announcement to all active user sessions. Leave empty to hide.')
                    ->schema([
                        TextInput::make('system_notice')
                            ->label('Notice Text')
                            ->maxLength(255)
                            ->placeholder('e.g. Scheduled maintenance on Sunday 02:00 UTC'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Commit Changes to Infrastructure')
                ->color('success')
                ->action(function () {
                    $formData = $this->form->getState();

                    cache(['max_payload_size' => $formData['max_payload_size']], now()->addYears(1));
                    cache(['system_notice'    => $formData['system_notice']],    now()->addYears(1));

                    Notification::make()
                        ->title('Security Policies Pushed Live!')
                        ->success()
                        ->send();
                }),
        ];
    }
}
