<?php

namespace App\Filament\Resources\Gyms\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Galeri Foto';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('image')
                ->label('Foto')
                ->image()
                ->required()
                ->disk('public')
                ->directory('gyms/gallery')
                ->visibility('public')
                ->maxSize(2048)
                ->helperText('JPG atau PNG, maksimal 2 MB.'),
            TextInput::make('caption')->label('Keterangan foto')->maxLength(150),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('caption')
            ->columns([
                ImageColumn::make('image')->label('Foto')->disk('public')->square(),
                TextColumn::make('caption')->label('Keterangan')->placeholder('-'),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}