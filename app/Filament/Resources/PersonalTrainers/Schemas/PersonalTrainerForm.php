<?php

namespace App\Filament\Resources\PersonalTrainers\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PersonalTrainerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'lg' => 12,
            ])
            ->extraAttributes(['class' => 'personal-trainer-form-grid'])
            ->components([
                Section::make('Profil pelatih')
                    ->description('Informasi utama yang ditampilkan kepada calon member.')
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 8,
                    ])
                    ->extraAttributes(['class' => 'personal-trainer-form-section'])
                    ->schema([
                        TextInput::make('name')->label('Nama pelatih')->required()->maxLength(100),
                        TextInput::make('specialization')->label('Spesialisasi')->required()->maxLength(120),
                        Textarea::make('description')
                            ->label('Deskripsi pelatih')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('photo_path')
                            ->label('Foto pelatih')
                            ->image()
                            ->disk('public')
                            ->directory('personal-trainers')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),
                Section::make('Detail layanan')
                    ->description('Atur pengalaman, harga, durasi, dan status pelatih.')
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 4,
                    ])
                    ->extraAttributes(['class' => 'personal-trainer-form-section personal-trainer-service-section'])
                    ->schema([
                        TextInput::make('experience_years')->label('Tahun pengalaman')->numeric()->minValue(0)->required(),
                        TextInput::make('session_price')->label('Harga per sesi')->numeric()->prefix('Rp')->minValue(1)->required(),
                        Select::make('session_minutes')->label('Durasi sesi')->options([120 => '2 jam', 180 => '3 jam'])->default(120)->required(),
                        Toggle::make('is_active')->label('Pelatih aktif')->default(true),
                    ]),
                Section::make('Sertifikasi')
                    ->description('Tambah dan atur urutan sertifikat yang dimiliki pelatih.')
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'personal-trainer-form-section personal-trainer-media-section'])
                    ->schema([
                        Repeater::make('certifications')
                            ->label('Daftar sertifikat')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama sertifikat')
                                    ->required()
                                    ->maxLength(150)
                                    ->live(onBlur: true),
                                FileUpload::make('document')
                                    ->label('Foto sertifikat (Opsional)')
                                    ->image()
                                    ->disk('public')
                                    ->directory('personal-trainers/certificates')
                                    ->visibility('public')
                                    ->maxSize(5120),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ])
                            ->itemLabel(fn (array $state): string => $state['name'] ?? 'Sertifikat baru')
                            ->reorderable()
                            ->collapsible()
                            ->collapseAllAction(fn (Action $action): Action => $action
                                ->label('Tutup semua')
                                ->icon('heroicon-m-chevron-double-up')
                                ->button()
                                ->color('gray')
                                ->outlined())
                            ->expandAllAction(fn (Action $action): Action => $action
                                ->label('Buka semua')
                                ->icon('heroicon-m-chevron-double-down')
                                ->button()
                                ->color('gray')
                                ->outlined())
                            ->extraFieldWrapperAttributes(['class' => 'gym-sponsor-field trainer-certificate-field'])
                            ->extraAttributes(['class' => 'gym-sponsor-repeater trainer-certificate-repeater'])
                            ->addActionLabel('Tambah sertifikat')
                            ->columnSpanFull(),
                        Hidden::make('certification_documents')
                            ->dehydrateStateUsing(fn (): array => []),
                    ]),
            ]);
    }
}
