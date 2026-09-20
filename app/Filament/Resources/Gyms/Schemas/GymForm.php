<?php

namespace App\Filament\Resources\Gyms\Schemas;

use App\Enums\Region;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class GymForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Informasi Utama')->schema([
                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('name')
                    ->label('Nama gym')
                    ->required()
                    ->maxLength(150)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, Set $set, ?string $state) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Dipakai di URL, mis. /gyms/fitzone-tanjungpinang'),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->label('Foto utama')
                    ->image()
                    ->disk('public')
                    ->directory('gyms')
                    ->visibility('public')
                    ->maxSize(2048) // 2 MB
                    ->helperText('JPG atau PNG, maksimal 2 MB.')
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('Alamat dan Lokasi')->schema([
                TextInput::make('address')
                    ->label('Alamat')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('region')
                    ->label('Wilayah')
                    ->options(Region::options())
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('district', null)), // reset kecamatan

                Select::make('district')
                    ->label('Kecamatan / area')
                    ->options(function (Get $get) {
                        $value = $get('region');
                        $region = $value instanceof Region ? $value : Region::tryFrom((string) $value);
                        $list = $region?->districts() ?? [];

                        return array_combine($list, $list);
                    })
                    ->searchable(),

                TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->minValue(-90)
                    ->maxValue(90)
                    ->placeholder('0.9187')
                    ->helperText('Di Google Maps, klik kanan pada lokasi gym, lalu klik angka koordinat untuk menyalin.'),

                TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->minValue(-180)
                    ->maxValue(180)
                    ->placeholder('104.4665'),
            ])->columns(2),

            Section::make('Kontak dan Jam Operasional')->schema([
                TextInput::make('phone')
                    ->label('Telepon')
                    ->tel()
                    ->maxLength(30),

                TextInput::make('whatsapp')
                    ->label('WhatsApp')
                    ->regex('/^[0-9]{8,15}$/')
                    ->placeholder('6281200000000')
                    ->helperText('Hanya angka, format 628xxxxxxxxxx. Tanpa tanda + atau spasi.'),

                TextInput::make('opening_hours')
                    ->label('Jam operasional')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('06:00 - 22:00'),
            ])->columns(2),

        ]);
    }
}