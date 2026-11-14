<?php

namespace App\Filament\Member\Pages;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan akun';

    protected static ?string $title = 'Pengaturan akun';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.member.pages.account-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Filament::auth()->user()->only(['name', 'phone']));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(Filament::auth()->user())
            ->statePath('data')
            ->components([
                Section::make('Informasi pribadi')
                    ->description('Perbarui nama dan nomor WhatsApp yang digunakan akunmu.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama lengkap')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Nomor WhatsApp')
                            ->tel()
                            ->required()
                            ->regex('/^[0-9]{8,15}$/')
                            ->helperText('Gunakan angka saja, contoh: 6281234567890.'),
                    ])
                    ->columns(2),
                Section::make('Ubah password')
                    ->description('Kosongkan bagian ini jika tidak ingin mengganti password.')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password baru')
                            ->password()
                            ->revealable()
                            ->rule(Password::default())
                            ->same('passwordConfirmation')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state)),
                        TextInput::make('passwordConfirmation')
                            ->label('Konfirmasi password baru')
                            ->password()
                            ->revealable()
                            ->required()
                            ->visible(fn (Get $get): bool => filled($get('password')))
                            ->dehydrated(false),
                        TextInput::make('currentPassword')
                            ->label('Password saat ini')
                            ->password()
                            ->revealable()
                            ->currentPassword(guard: Filament::getAuthGuard())
                            ->required()
                            ->visible(fn (Get $get): bool => filled($get('password')))
                            ->dehydrated(false),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        $attributes = Arr::only($this->form->getState(), ['name', 'phone', 'password']);

        Filament::auth()->user()->update($attributes);
        $this->data['password'] = null;
        $this->data['passwordConfirmation'] = null;
        $this->data['currentPassword'] = null;

        Notification::make()
            ->title('Pengaturan akun berhasil disimpan')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan perubahan')
                ->submit('save'),
        ];
    }
}
