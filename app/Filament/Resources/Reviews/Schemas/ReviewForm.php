<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Models\Review;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')->label('User')->relationship('user', 'name')->disabled(),
            Select::make('gym_id')->label('Gym')->relationship('gym', 'name')->disabled(),
            TextInput::make('rating')->label('Rating (1-5)')->disabled(),

            Select::make('status')->label('Status')
                ->options([
                    Review::STATUS_APPROVED => 'Tampil (approved)',
                    Review::STATUS_REJECTED => 'Disembunyikan (rejected)',
                ])
                ->required(),

            Textarea::make('review')->label('Komentar')->rows(4)->disabled()->columnSpanFull(),
        ]);
    }
}