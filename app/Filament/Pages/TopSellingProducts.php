<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\SaleItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class TopSellingProducts extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Reporting & Analytics';

    protected static string $view = 'filament.pages.top-selling-products';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SaleItem::query()
                    ->selectRaw('product_id, sum(quantity) as total_quantity')
                    ->groupBy('product_id')
                    ->orderBy('total_quantity', 'desc')
            )
            ->columns([
                TextColumn::make('product.name'),
                TextColumn::make('total_quantity'),
            ]);
    }
}
