<?php

namespace App\Filament\Resources\MembershipPlans\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class MembershipPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(['default' => 1, 'md' => 2])
                ->schema([
                    TextInput::make('name')->label('Nama Paket')->required()->maxLength(100),

                    TextInput::make('duration_days')->label('Durasi (hari)')->required()
                        ->numeric()->integer()->minValue(1)->maxValue(3650),

                    TextInput::make('price')->label('Harga')->required()
                        ->numeric()->integer()->minValue(0)->maxValue(100000000)->prefix('Rp'),

                    TextInput::make('original_price')->label('Harga Sebelum Diskon (Opsional)')
                        ->numeric()->integer()->minValue(0)->maxValue(100000000)->prefix('Rp'),

                    TextInput::make('discount_label')->label('Label Diskon (Opsional)')
                        ->maxLength(40)->placeholder('Contoh: 20% OFF'),

                    TextInput::make('badge')->label('Badge Paket (Opsional)')
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
                        ->columnSpanFull(),

                    Toggle::make('is_active')
                        ->label('Aktif (ditampilkan di halaman publik)')
                        ->default(true)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }
}
