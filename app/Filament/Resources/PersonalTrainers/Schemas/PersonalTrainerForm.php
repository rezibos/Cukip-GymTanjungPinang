<?php

namespace App\Filament\Resources\PersonalTrainers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PersonalTrainerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama pelatih')->required()->maxLength(100),
                TextInput::make('specialization')->label('Spesialisasi')->required()->maxLength(120),
                Textarea::make('description')->label('Deskripsi pelatih')->helperText('Jelaskan pendekatan latihan, keahlian utama, dan bentuk pendampingan selama sesi privat.')->rows(6)->required()->columnSpanFull(),
                TextInput::make('experience_years')->label('Tahun pengalaman')->numeric()->minValue(0)->required(),
                TagsInput::make('certifications')->label('Nama sertifikasi')->helperText('Tekan Enter setelah menulis setiap sertifikat.')->columnSpanFull(),
                FileUpload::make('certification_documents')->label('Foto sertifikat')->helperText('Opsional. Unggah foto sertifikat yang dapat dilihat calon pelanggan.')->image()->multiple()->reorderable()->maxFiles(10)->disk('public')->directory('personal-trainers/certificates')->visibility('public')->columnSpanFull(),
                TextInput::make('session_price')->label('Harga per sesi')->numeric()->prefix('Rp')->minValue(1)->required(),
                Select::make('session_minutes')->label('Durasi sesi')->options([120 => '2 jam', 180 => '3 jam'])->default(120)->required(),
                FileUpload::make('photo_path')->label('Foto pelatih')->image()->disk('public')->directory('personal-trainers')->visibility('public'),
                Toggle::make('is_active')->label('Aktif')->default(true),
            ]);
    }
}
