<?php

namespace App\Filament\Resources\GymLocations\Schemas;

use App\Support\CoordinateParser;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class GymLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama lokasi')
                    ->required()
                    ->maxLength(150),
                TextInput::make('address')
                    ->label('Alamat / wilayah')
                    ->required()
                    ->maxLength(255),
                TextInput::make('map_paste')
                    ->label('Tempel lokasi dari Google Maps')
                    ->helperText('Tempel koordinat, link, atau kode embed. Latitude dan longitude akan terisi otomatis.')
                    ->dehydrated(false)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        $coordinates = filled($state) ? CoordinateParser::parse($state) : null;

                        if ($coordinates === null) {
                            return;
                        }

                        $set('latitude', $coordinates['lat']);
                        $set('longitude', $coordinates['lng']);
                        $set('map_paste', null);
                    })
                    ->columnSpanFull(),
                TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->required()
                    ->minValue(-90)
                    ->maxValue(90),
                TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->required()
                    ->minValue(-180)
                    ->maxValue(180),
                TextInput::make('opening_hours')
                    ->label('Jam operasional')
                    ->required()
                    ->maxLength(150),
                TextInput::make('whatsapp')
                    ->label('WhatsApp')
                    ->helperText('Gunakan angka saja, misalnya 6281234567890.')
                    ->regex('/^[0-9]{8,15}$/')
                    ->maxLength(20),
                FileUpload::make('image_path')
                    ->label('Foto lokasi')
                    ->helperText('Opsional. Foto ini tampil pada kartu lokasi di halaman utama. Maksimal 5 MB.')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imageEditor()
                    ->imageEditorAspectRatioOptions(['4:3', '16:9'])
                    ->disk('public')
                    ->directory('gym-locations')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->columnSpanFull(),
                Select::make('image_position')
                    ->label('Fokus foto')
                    ->options([
                        'center' => 'Tengah',
                        'left center' => 'Kiri',
                        '35% center' => 'Agak kiri',
                        'right center' => 'Kanan',
                        '65% center' => 'Agak kanan',
                        'center top' => 'Atas',
                        'center bottom' => 'Bawah',
                    ])
                    ->default('center')
                    ->required(),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->default(0),
                Toggle::make('is_active')
                    ->label('Tampilkan di peta')
                    ->default(true),
            ])
            ->columns(2);
    }
}
