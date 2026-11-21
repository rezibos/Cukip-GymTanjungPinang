<?php

namespace App\Providers\Filament;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Enums\ActionStatus;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\FontProviders\LocalFontProvider;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Tables\Filters\SelectFilter;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Js;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Livewire\Component;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        TextInput::configureUsing(fn (TextInput $input): TextInput => $input
            ->placeholder(fn (TextInput $component): string => self::textInputPlaceholder($component)));

        Textarea::configureUsing(fn (Textarea $textarea): Textarea => $textarea
            ->placeholder(fn (Textarea $component): string => self::textareaPlaceholder($component)));

        Select::configureUsing(fn (Select $select): Select => $select
            ->native(false)
            ->searchable()
            ->extraFieldWrapperAttributes(['class' => 'fi-fixed-positioning-context'], merge: true)
            ->position('bottom'));

        SelectFilter::configureUsing(fn (SelectFilter $filter): SelectFilter => $filter
            ->native(false)
            ->searchable()
            ->preload());

        Lang::get('filament-tables::table.selection_indicator.selected_count', locale: 'en');
        Lang::addLines([
            'table.selection_indicator.selected_count' => '1 data dipilih|:count data dipilih',
            'table.selection_indicator.actions.select_all.label' => 'Pilih semua :count',
            'table.selection_indicator.actions.deselect_all.label' => 'Batalkan semua pilihan',
            'table.filters.indicator' => 'Filter aktif',
            'table.filters.actions.remove_all.tooltip' => 'Hapus semua filter',
        ], 'en', 'filament-tables');

        CreateAction::configureUsing(function (CreateAction $action): void {
            $action
                ->successNotification(null)
                ->after(function (CreateAction $action, Component $livewire): void {
                    if ($action->getStatus() === ActionStatus::Success) {
                        $livewire->dispatch('admin-record-created');
                    }
                });
        });

        DeleteAction::configureUsing(function (DeleteAction $action): void {
            $action
                ->label('Hapus')
                ->modalSubmitActionLabel('Hapus')
                ->requiresConfirmation(false)
                ->modal(false)
                ->keyBindings([])
                ->successNotification(null)
                ->alpineClickHandler(function (DeleteAction $action): string {
                    $context = Js::from($action->getContext())->toHtml();

                    return "window.confirmAdminDelete().then((isConfirmed) => { if (isConfirmed) { \$wire.mountAction('delete', {}, {$context}) } })";
                })
                ->after(function (DeleteAction $action, Component $livewire): void {
                    if ($action->getStatus() === ActionStatus::Success) {
                        $livewire->dispatch('admin-record-deleted');
                    }
                });
        });

        DeleteBulkAction::configureUsing(function (DeleteBulkAction $action): void {
            $action
                ->label('Hapus semua')
                ->requiresConfirmation(false)
                ->modal(false)
                ->keyBindings([])
                ->successNotification(null)
                ->alpineClickHandler(function (DeleteBulkAction $action): string {
                    $context = Js::from($action->getContext())->toHtml();

                    return "window.confirmAdminDelete().then((isConfirmed) => { if (isConfirmed) { \$wire.mountAction('delete', {}, {$context}) } })";
                })
                ->after(function (DeleteBulkAction $action, Component $livewire): void {
                    if ($action->getStatus() === ActionStatus::Success) {
                        $livewire->dispatch('admin-record-deleted');
                    }
                });
        });

        ForceDeleteAction::configureUsing(fn (ForceDeleteAction $action): ForceDeleteAction => $action
            ->label('Hapus permanen')
            ->modalSubmitActionLabel('Hapus permanen'));

        ForceDeleteBulkAction::configureUsing(fn (ForceDeleteBulkAction $action): ForceDeleteBulkAction => $action
            ->label('Hapus permanen yang dipilih')
            ->modalSubmitActionLabel('Hapus permanen'));
    }

    private static function textInputPlaceholder(TextInput $component): string
    {
        $name = $component->getName();
        $label = strtolower((string) $component->getLabel());

        return match (true) {
            str_contains($label, 'nama pelatih') => 'Contoh: Kak Bayu',
            str_contains($label, 'nama kelas') => 'Contoh: Yoga Pagi',
            str_contains($label, 'nama fasilitas') => 'Contoh: Treadmill',
            str_contains($label, 'nama paket') => 'Contoh: 6 Month',
            str_contains($label, 'nama sponsor') => 'Contoh: FORMA.',
            str_contains($label, 'nama lokasi') => 'Contoh: CukipGym Pusat Kota',
            str_contains($label, 'nama sertifikat') => 'Contoh: Certified Personal Trainer',
            str_contains($label, 'nama tampilan') => 'Contoh: Fachrezi Bachri',
            $name === 'name' => 'Contoh: Fachrezi Bachri',
            $name === 'email' => 'Contoh: fachrezi@email.com',
            in_array($name, ['phone', 'whatsapp'], true) => 'Contoh: 081234567890',
            in_array($name, ['password', 'passwordConfirmation'], true) => 'Minimal 8 karakter',
            $name === 'currentPassword' => 'Password akun saat ini',
            $name === 'specialization' => 'Contoh: Functional Training',
            $name === 'instructor' => 'Contoh: Kak Rina',
            in_array($name, ['capacity', 'quantity'], true) => 'Contoh: 15',
            $name === 'experience_years' => 'Contoh: 3',
            $name === 'weeks' => 'Contoh: 4',
            $name === 'duration_days' => 'Contoh: 30',
            in_array($name, ['price', 'original_price', 'session_price', 'amount', 'formatted_amount'], true) => 'Contoh: 150000',
            $name === 'rating' => 'Contoh: 5',
            $name === 'address' => 'Contoh: Jl. Sudirman No. 10',
            $name === 'map_paste' => 'Tempel tautan atau koordinat Google Maps',
            $name === 'latitude' => 'Contoh: 0.920803',
            $name === 'longitude' => 'Contoh: 104.487397',
            $name === 'opening_hours' => 'Contoh: 06:00 - 22:00',
            $name === 'sort_order' => 'Contoh: 1',
            $name === 'invoice_number' => 'Contoh: INV-20261121-0001',
            $name === 'purchase_type' => 'Contoh: Membership',
            $name === 'purchase_name' => 'Contoh: Paket 6 Month',
            $name === 'transaction_date' => 'Contoh: 21/11/2026 10:00',
            $name === 'user_name' => 'Contoh: Fachrezi Bachri',
            $name === 'discount_label' => 'Contoh: Hemat 20%',
            $name === 'badge' => 'Contoh: Best Value',
            $name === 'benefit' => 'Contoh: Akses gym setiap hari',
            $name === 'url' => 'Contoh: https://example.com',
            $name === 'tagline' => 'Contoh: Active Club',
            $name === 'footer_location' => 'Contoh: Gym lokal Tanjungpinang',
            str_starts_with($name, 'hero_') => 'Teks yang tampil pada halaman utama',
            str_starts_with($name, 'about_') => 'Teks yang tampil pada bagian Tentang CukipGym',
            default => 'Tulis '.strtolower((string) $component->getLabel()).' di sini',
        };
    }

    private static function textareaPlaceholder(Textarea $component): string
    {
        return match ($component->getName()) {
            'description' => 'Jelaskan informasi yang perlu diketahui pengunjung.',
            'admin_notes' => 'Tambahkan catatan admin jika diperlukan.',
            'footer_description' => 'Tuliskan deskripsi singkat yang tampil pada footer.',
            default => 'Tuliskan informasi lengkap di sini.',
        };
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(fn (): RedirectResponse => redirect()->route('login'))
            ->brandName('CukipGym')
            ->font('Poppins', provider: LocalFontProvider::class)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->darkMode(false)
            ->maxContentWidth(Width::Full)
            ->sidebarWidth('16rem')
            ->sidebarFullyCollapsibleOnDesktop()
            ->renderHook(PanelsRenderHook::TOPBAR_START, fn (): View => view('filament.partials.sidebar-toggle'))
            ->renderHook(PanelsRenderHook::SCRIPTS_AFTER, fn (): View => view('filament.partials.admin-scripts'))
            ->navigationGroups(['Operasional', 'Kelas & latihan', 'Konten website'])
            ->colors([
                'primary' => Color::Neutral,
                'gray' => Color::Neutral,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
