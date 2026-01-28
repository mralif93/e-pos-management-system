<?php

namespace App\Providers\Filament;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\ApplyUserTheme::class,
            ])
            ->navigationGroups([
                'User Management',
                'Outlet Management',
                'Product Management',
                'Sale Management',
                'LHDN e-Invoice',
                'Reporting & Analytics',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups(true);
    }

    public function boot()
    {
        \Filament\Support\Facades\FilamentIcon::register([
            'panels::sidebar.group.collapse-button' => 'heroicon-o-chevron-down',
        ]);

        Filament::registerRenderHook(
            PanelsRenderHook::TOPBAR_END,
            fn(): string => Blade::render('@livewire(\'admin.outlet-switcher\')'),
        );

        // Custom CSS to handle the rotation for Right -> Down behavior
        Filament::registerRenderHook(
            PanelsRenderHook::STYLES_AFTER,
            fn() => Blade::render('<style>
                .fi-sidebar-group-collapse-button svg {
                    transition: transform 0.2s;
                    transform: rotate(90deg); /* Default closed (Right) */
                }
                .fi-sidebar-group-collapse-button[aria-expanded="true"] svg {
                    transform: rotate(0deg); /* Open (Down) */
                }
                .fi-main { max-width: 100% !important; }
            </style>'),
        );
    }
}
