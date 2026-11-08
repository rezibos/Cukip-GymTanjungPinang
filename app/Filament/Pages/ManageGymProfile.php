<?php

namespace App\Filament\Pages;

use App\Models\GymProfile;
use App\Support\CoordinateParser;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ManageGymProfile extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Profil Gym';

    protected static ?string $title = 'Profil Gym';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten website';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.manage-gym-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(GymProfile::current()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('name')->label('Nama Gym')->required()->maxLength(150),

                Textarea::make('description')->label('Deskripsi')->required()->rows(4),

                TextInput::make('address')->label('Alamat')->required()->maxLength(255),

                TextInput::make('map_paste')
                    ->label('Tempel Lokasi dari Google Maps')
                    ->placeholder('Tempel koordinat, link, atau kode embed')
                    ->helperText('Klik kanan lokasi di Google Maps lalu klik angka koordinat, atau tempel link/kode embed. Latitude & longitude di bawah terisi otomatis.')
                    ->dehydrated(false)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        if (blank($state)) {
                            return;
                        }

                        $coords = CoordinateParser::parse($state);

                        if ($coords) {
                            $set('latitude', $coords['lat']);
                            $set('longitude', $coords['lng']);
                            $set('map_paste', null);

                            Notification::make()->title('Koordinat terisi')->success()->send();
                        } else {
                            Notification::make()
                                ->title('Koordinat tidak ditemukan')
                                ->body('Coba salin koordinat lewat klik kanan di Google Maps, atau pakai link dari address bar.')
                                ->warning()
                                ->send();
                        }
                    })
                    ->columnSpanFull(),

                TextInput::make('latitude')->label('Latitude')->numeric()->minValue(-90)->maxValue(90),
                TextInput::make('longitude')->label('Longitude')->numeric()->minValue(-180)->maxValue(180),

                TextInput::make('opening_hours')->label('Jam Operasional')->required()->placeholder('06:00 - 22:00'),

                TextInput::make('phone')->label('Telepon')->tel(),
                TextInput::make('whatsapp')->label('WhatsApp')
                    ->regex('/^[0-9]{8,15}$/')
                    ->placeholder('6281200000000')
                    ->helperText('Hanya angka, format 628xxxxxxxxxx.'),

                FileUpload::make('image')->label('Foto Gym')
                    ->image()->disk('public')->directory('gym-profile')
                    ->visibility('public')->maxSize(2048)->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function save(): void
    {
        $validated = $this->form->getState();

        GymProfile::current()->update($validated);

        Notification::make()->title('Profil gym berhasil disimpan')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')->label('Simpan')->submit('save'),
        ];
    }
}
