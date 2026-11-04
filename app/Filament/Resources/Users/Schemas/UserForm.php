<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama')->required()->maxLength(100),

            TextInput::make('email')->label('Email')->email()->required()->maxLength(191)
                ->unique(ignoreRecord: true),

            TextInput::make('phone')->label('Telepon')->tel()->maxLength(20),

            Select::make('role')->label('Role')
                ->options(['user' => 'user', 'admin' => 'admin'])
                ->required()
                // Admin tidak boleh menurunkan role dirinya sendiri
                ->disabled(fn (?User $record) => $record?->id === auth()->id()),

            TextInput::make('password')->label('Password')
                ->password()->revealable()->minLength(8)
                ->required(fn (string $operation) => $operation === 'create')
                ->dehydrated(fn (?string $state) => filled($state))
                ->helperText('Kosongkan saat edit jika tidak ingin mengganti password.'),
        ]);
    }
}