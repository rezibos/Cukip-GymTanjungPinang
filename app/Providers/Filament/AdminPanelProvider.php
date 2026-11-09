<?php

namespace App\Providers\Filament;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Enums\ActionStatus;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Js;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Livewire\Component;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
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
