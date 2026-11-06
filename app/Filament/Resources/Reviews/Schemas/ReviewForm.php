<?php

namespace App\Filament\Resources\Reviews\Schemas;

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
            TextInput::make('name')->label('Nama tampilan')->disabled(),
            TextInput::make('rating')->label('Rating (1-5)')->disabled(),
            Select::make('status')->label('Status')
                ->options(['approved' => 'Tampil', 'rejected' => 'Disembunyikan'])
                ->required(),
            Textarea::make('review')->label('Komentar')->rows(4)->disabled()->columnSpanFull(),
        ]);
    }
}
