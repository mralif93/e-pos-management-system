<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Landing extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.landing';

    // Hide from navigation menu
    protected static bool $shouldRegisterNavigation = false;

    // Set custom route
    public static function getSlug(): string
    {
        return 'landing';
    }

    // Remove page title
    protected ?string $heading = '';

    // Get user data for the view
    protected function getViewData(): array
    {
        return [
            'user' => auth()->user(),
        ];
    }

    // Use simple layout without sidebar
    public function getLayout(): string
    {
        return 'filament-panels::components.layout.simple';
    }
}
