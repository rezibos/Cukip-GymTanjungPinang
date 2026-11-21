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
            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->unique(ignoreRecord: true)
                ->disabledOn('edit'),
            TextInput::make('name')
                ->label('Nama tampilan')
                ->required()
                ->maxLength(100)
                ->disabledOn('edit'),
            Select::make('rating')
                ->label('Rating')
                ->options([
                    5 => '5 bintang',
                    4 => '4 bintang',
                    3 => '3 bintang',
                    2 => '2 bintang',
                    1 => '1 bintang',
                ])
                ->required()
                ->disabledOn('edit'),
            Select::make('status')->label('Status')
                ->options(['approved' => 'Tampil', 'rejected' => 'Disembunyikan'])
                ->default('approved')
                ->required(),
            Textarea::make('review')
                ->label('Komentar')
                ->required()
                ->minLength(10)
                ->maxLength(2000)
                ->rows(4)
                ->disabledOn('edit')
                ->columnSpanFull(),
        ]);
    }
}
