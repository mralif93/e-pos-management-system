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
                \App\Models\Product::query()
                    ->withSum('saleItems', 'quantity')
                    ->whereHas('saleItems')
                    ->orderBy('sale_items_sum_quantity', 'desc')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable(),
                TextColumn::make('sale_items_sum_quantity')
                    ->label('Total Sold')
                    ->sortable(),
            ]);
    }
}
