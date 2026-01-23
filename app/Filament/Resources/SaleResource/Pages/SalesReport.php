<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use Filament\Resources\Pages\Page;
use App\Filament\Widgets\StatsOverviewWidget;

class SalesReport extends Page
{
    protected static string $resource = SaleResource::class;

    protected static string $view = 'filament.resources.sale-resource.pages.sales-report';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
        ];
    }
}
