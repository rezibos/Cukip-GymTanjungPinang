<?php

namespace App\Filament\Resources\MembershipPlans\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MembershipPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama Paket')->required()->maxLength(100),

            TextInput::make('duration_days')->label('Durasi (hari)')->required()
                ->numeric()->integer()->minValue(1)->maxValue(3650)
                ->helperText('Contoh: 1 (harian), 7 (mingguan), 30 (bulanan), 365 (tahunan).'),

            TextInput::make('price')->label('Harga')->required()
                ->numeric()->integer()->minValue(0)->maxValue(100000000)->prefix('Rp'),

            TextInput::make('original_price')->label('Harga Sebelum Diskon')
                ->numeric()->integer()->minValue(0)->maxValue(100000000)->prefix('Rp')
                ->helperText('Opsional. Ditampilkan sebagai harga yang dicoret.'),

            TextInput::make('discount_label')->label('Label Diskon')
                ->maxLength(40)->placeholder('Contoh: 20% OFF'),

            TextInput::make('badge')->label('Badge Paket')
                ->maxLength(60)->placeholder('Contoh: Trial atau Best Value'),

            Textarea::make('description')->label('Deskripsi')->rows(3)->columnSpanFull(),

            Repeater::make('benefits')->label('Benefit Pembelian')
                ->simple(
                    TextInput::make('benefit')
                        ->required()
                        ->maxLength(160),
                )
                ->defaultItems(0)
                ->addActionLabel('Tambah benefit')
                ->helperText('Bonus khusus paket, bukan daftar fasilitas umum gym.')
                ->columnSpanFull(),

            Toggle::make('is_active')->label('Aktif (ditampilkan di halaman publik)')->default(true),
        ]);
    }
}
