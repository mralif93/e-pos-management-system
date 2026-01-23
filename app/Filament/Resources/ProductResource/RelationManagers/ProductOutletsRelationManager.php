<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\Outlet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductOutletsRelationManager extends RelationManager
{
    protected static string $relationship = 'outlets';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('outlet_id')
                    ->label('Outlet')
                    ->options(Outlet::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->disabled(fn (string $operation): bool => $operation === 'edit'),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->prefix('$'),
                Forms\Components\TextInput::make('stock_level')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('outlet.name')
                    ->label('Outlet')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_level')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
