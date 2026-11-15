<?php

namespace App\Filament\Pages;

use App\Models\GymProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageGymProfile extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationLabel = 'Tentang CukipGym';

    protected static ?string $title = 'Tentang CukipGym';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten website';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.manage-gym-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $profile = GymProfile::current();
        $data = $profile->toArray();

        foreach ([
            'about_eyebrow',
            'about_location_label',
            'about_title_line_one',
            'about_title_line_two',
            'about_title_line_three',
            'about_description_primary',
            'about_description_secondary',
            'about_gallery_eyebrow',
            'about_gallery_title',
            'footer_description',
            'footer_location',
        ] as $key) {
            $data[$key] = $profile->content($key);
        }

        $data['footer_sponsors'] = $profile->footerSponsors(onlyActive: false);

        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Konten Tentang')
                    ->description('Tulisan yang muncul pada bagian Tentang CukipGym di beranda.')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])->schema([
                            TextInput::make('about_eyebrow')->label('Label Tentang')->required()->maxLength(120),
                            TextInput::make('about_location_label')->label('Label lokasi')->required()->maxLength(120),
                            TextInput::make('about_title_line_one')->label('Judul baris pertama')->required()->maxLength(100),
                            TextInput::make('about_title_line_two')->label('Judul baris kedua')->required()->maxLength(100),
                            TextInput::make('about_title_line_three')->label('Judul baris ketiga')->required()->maxLength(100),
                        ]),
                        Textarea::make('about_description_primary')->label('Deskripsi utama')
                            ->required()->rows(4)->maxLength(800),
                        Textarea::make('about_description_secondary')->label('Deskripsi tambahan')
                            ->required()->rows(4)->maxLength(800),
                    ])
                    ->columns(1),
                Section::make('Galeri Tentang')
                    ->description('Kelima foto mengikuti posisi kolase yang sudah ada dan dapat diganti satu per satu.')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])->schema([
                            TextInput::make('about_gallery_eyebrow')->label('Label pada foto utama')
                                ->required()->maxLength(120),
                            Textarea::make('about_gallery_title')->label('Judul pada foto utama')
                                ->helperText('Gunakan baris baru untuk memisahkan tulisan.')
                                ->required()->rows(2)->maxLength(160),
                        ]),
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])->schema([
                            $this->galleryUpload('about_gallery_image_one', 'Foto 1 — utama'),
                            $this->galleryUpload('about_gallery_image_two', 'Foto 2 — kanan atas lebar'),
                            $this->galleryUpload('about_gallery_image_three', 'Foto 3 — kanan atas'),
                            $this->galleryUpload('about_gallery_image_four', 'Foto 4 — tengah bawah'),
                            $this->galleryUpload('about_gallery_image_five', 'Foto 5 — kanan bawah'),
                        ]),
                    ]),
                Section::make('Footer')
                    ->description('Atur deskripsi singkat dan teks lokasi pada bagian paling bawah website.')
                    ->schema([
                        Textarea::make('footer_description')->label('Deskripsi footer')
                            ->required()->rows(4)->maxLength(500),
                        TextInput::make('footer_location')->label('Teks lokasi')
                            ->required()->maxLength(150),
                    ]),
                Section::make('Sponsor & partner')
                    ->description('Tambah, urutkan, nonaktifkan, atau ganti logo sponsor yang tampil di footer.')
                    ->schema([
                        Repeater::make('footer_sponsors')
                            ->label('Daftar sponsor')
                            ->schema([
                                TextInput::make('name')->label('Nama sponsor')->required()->maxLength(100),
                                TextInput::make('tagline')->label('Tagline')->maxLength(100),
                                FileUpload::make('logo')->label('Logo')
                                    ->helperText('Opsional. Jika kosong, nama sponsor akan ditampilkan sebagai teks.')
                                    ->image()->disk('public')->directory('homepage/sponsors')
                                    ->visibility('public')->maxSize(5120),
                                TextInput::make('url')->label('Tautan')->url()->maxLength(255),
                                Toggle::make('is_active')->label('Tampilkan')->default(true),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Sponsor baru')
                            ->reorderable()
                            ->collapsible()
                            ->addActionLabel('Tambah sponsor')
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1);
    }

    public function save(): void
    {
        GymProfile::current()->update($this->form->getState());

        Notification::make()->title('Tentang CukipGym berhasil disimpan')->success()->send();
    }

    public function getPageClasses(): array
    {
        return ['gym-profile-page'];
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::getUrl() => 'Tentang CukipGym',
            'Edit',
        ];
    }

    private function galleryUpload(string $name, string $label): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->image()
            ->disk('public')
            ->directory('homepage/about')
            ->visibility('public')
            ->maxSize(5120)
            ->imagePreviewHeight('180');
    }
}
