<?php

namespace App\Filament\Pages;

use App\Models\GymProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageHomepage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Halaman Awal';

    protected static ?string $title = 'Halaman Awal';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten website';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.manage-homepage';

    public ?array $data = [];

    public function mount(): void
    {
        $profile = GymProfile::current();

        $this->form->fill([
            'hero_image' => $profile->hero_image,
            'hero_eyebrow' => $profile->content('hero_eyebrow'),
            'hero_title_primary' => $profile->content('hero_title_primary'),
            'hero_title_secondary' => $profile->content('hero_title_secondary'),
            'hero_description' => $profile->content('hero_description'),
            'hero_cta_label' => $profile->content('hero_cta_label'),
            'hero_about_label' => $profile->content('hero_about_label'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Hero beranda')
                    ->description('Atur foto dan tulisan pertama yang dilihat pengunjung saat membuka website.')
                    ->schema([
                        FileUpload::make('hero_image')
                            ->label('Foto hero')
                            ->helperText('Gunakan foto horizontal beresolusi tinggi. Foto yang sama dipakai untuk efek abu-abu dan berwarna.')
                            ->image()
                            ->disk('public')
                            ->directory('homepage/hero')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->imagePreviewHeight('320')
                            ->columnSpanFull(),
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])->schema([
                            TextInput::make('hero_eyebrow')
                                ->label('Label kecil')
                                ->required()
                                ->maxLength(120),
                            TextInput::make('hero_cta_label')
                                ->label('Teks tombol utama')
                                ->required()
                                ->maxLength(80),
                            TextInput::make('hero_title_primary')
                                ->label('Judul utama')
                                ->required()
                                ->maxLength(120),
                            TextInput::make('hero_title_secondary')
                                ->label('Judul outline')
                                ->required()
                                ->maxLength(120),
                        ]),
                        Textarea::make('hero_description')
                            ->label('Deskripsi')
                            ->required()
                            ->rows(4)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        TextInput::make('hero_about_label')
                            ->label('Teks tautan ke bagian Tentang')
                            ->required()
                            ->maxLength(100),
                    ])
                    ->columns(1),
            ]);
    }

    public function save(): void
    {
        GymProfile::current()->update($this->form->getState());

        $this->dispatch(
            'filament-sweetalert',
            title: 'Halaman awal berhasil disimpan',
            message: 'Konten hero website sudah diperbarui.',
            type: 'success',
        );
    }

    public function getPageClasses(): array
    {
        return ['homepage-content-page'];
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::getUrl() => 'Halaman Awal',
            'Edit',
        ];
    }
}
