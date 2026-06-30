<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\FileUpload::make('avatar')
                ->label('Profile Picture')
                ->image() // Validates that the file is an image
                ->avatar() // Shapes the uploader into a professional circular avatar preview
                ->disk('firebase') // 🌟 Forces Filament to save files straight to your Firebase bucket
                ->directory('avatars') // Stores it inside an "avatars/" folder in your cloud storage
                ->visibility('public')
                ->maxSize(2048), // Limit file size to 2MB for profile pics
                
                // 👤 Name Input Card
                Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
    
                // 📧 Email Input Card
                Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
    
                // 🔑 Password Input Card (Hidden on edit screen unless changing it)
                Components\TextInput::make('password')
                    ->password()
                    // Force strict validation rules inside the admin panel forms
                    ->rule(Password::defaults()) 
                    // Only require password on creation, make optional on profile updates
                    ->required(fn (string $context): bool => $context === 'create')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->maxLength(255),
    
                // 🛡️ Admin Switch Toggle
                Components\Toggle::make('is_admin')
                    ->label('Give Admin Access')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Displays the user avatar using your custom attribute logic!
                Tables\Columns\ImageColumn::make('wirechat_avatar_url')
                    ->label('Avatar')
                    ->circular(),
    
                // Standard details with a built-in search bar feature
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
    
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
    
                // Displays their admin status as a clean green/red toggle icon
                Tables\Columns\IconColumn::make('is_admin')
                    ->boolean()
                    ->label('Admin')
                    ->sortable(),
    
                // Tracks registration date
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Joined')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Let the admin quickly filter out who is an admin vs a regular user
                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('Administrator Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
