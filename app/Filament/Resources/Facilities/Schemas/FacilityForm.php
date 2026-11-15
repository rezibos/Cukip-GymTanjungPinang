<?php

namespace App\Filament\Resources\Facilities\Schemas;

use App\Enums\FacilityCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama Fasilitas')->required()->maxLength(100),
            Select::make('category')->label('Kategori')->options(FacilityCategory::options())->default(FacilityCategory::Support->value)->required(),
            Textarea::make('description')->label('Keterangan')->rows(4)->maxLength(255)->columnSpanFull(),
            FileUpload::make('image_path')
                ->label('Foto fasilitas')
                ->helperText('Opsional. Foto akan muncul saat pengunjung mengarahkan mouse ke card fasilitas.')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->imageEditor()
                ->imageEditorAspectRatioOptions(['4:3', '16:9'])
                ->disk('public')
                ->directory('facilities')
                ->visibility('public')
                ->maxSize(4096)
                ->columnSpanFull(),
        ]);
    }
}
