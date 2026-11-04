<?php

namespace App\Filament\Resources\MembershipPlans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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

            Textarea::make('description')->label('Deskripsi')->rows(3)->columnSpanFull(),

            Toggle::make('is_active')->label('Aktif (ditampilkan di halaman publik)')->default(true),
        ]);
    }
}