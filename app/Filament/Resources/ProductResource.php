<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Repeater;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag'; // Changed

    protected static ?string $navigationGroup = 'Product Management'; // Added

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Details')
                    ->description('Manage product information.')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('has_variants')
                            ->live(),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->prefix('$')
                            ->hidden(fn(Forms\Get $get) => $get('has_variants')),
                        Forms\Components\TextInput::make('cost')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->prefix('$')
                            ->hidden(fn(Forms\Get $get) => $get('has_variants')),
                        Forms\Components\TextInput::make('stock_level')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->hidden(fn(Forms\Get $get) => $get('has_variants')),
                        Repeater::make('variants')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->default(0.00)
                                    ->prefix('$'),
                                Forms\Components\TextInput::make('cost')
                                    ->required()
                                    ->numeric()
                                    ->default(0.00)
                                    ->prefix('$'),
                                Forms\Components\TextInput::make('stock_level')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(2)
                            ->visible(fn(Forms\Get $get) => $get('has_variants')),
                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                    ])->columns(2),
                Section::make('Outlet Pricing')
                    ->description('Set specific prices for each outlet.')
                    ->schema([
                        Repeater::make('prices')
                            ->relationship()
                            ->modifyQueryUsing(
                                fn(Builder $query) =>
                                auth()->user()->role !== 'Super Admin' && auth()->user()->outlet_id
                                ? $query->where('outlet_id', auth()->user()->outlet_id)
                                : $query
                            )
                            ->schema([
                                Forms\Components\Select::make('outlet_id')
                                    ->relationship(
                                        'outlet',
                                        'name',
                                        modifyQueryUsing: fn(Builder $query) =>
                                        auth()->user()->role !== 'Super Admin' && auth()->user()->outlet_id
                                        ? $query->where('id', auth()->user()->outlet_id)
                                        : $query
                                    )
                                    ->default(fn() => auth()->user()->role !== 'Super Admin' ? auth()->user()->outlet_id : null)
                                    ->disabled(fn() => auth()->user()->role !== 'Super Admin')
                                    ->dehydrated()
                                    ->required(),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->default(0.00)
                                    ->prefix('$'),
                            ])
                            ->columns(2)
                            ->itemLabel(fn(array $state): ?string => \App\Models\Outlet::find($state['outlet_id'])?->name),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_level')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->options(Category::all()->pluck('name', 'id'))
                    ->label('Category'),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->label(false)
                    ->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label(false)
                    ->tooltip('Delete'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->role === 'Super Admin') {
            $selectedOutletId = Session::get('selected_super_admin_outlet_id');
            if ($selectedOutletId) {
                // Filter by selected outlet for Super Admin
                $query->whereHas('prices', function (Builder $query) use ($selectedOutletId) {
                    $query->where('outlet_id', $selectedOutletId);
                });
            }
            // If no outlet is selected for Super Admin, they see all products by default,
            // or perhaps a default outlet's products. For now, seeing all is default.
            // If we want to force Super Admin to select an outlet,
            // we'd add logic here to filter by a default or simply return an empty query.
        } else {
            // For all other roles, filter by their assigned outlet.
            if ($user->outlet_id) {
                $query->whereHas('prices', function (Builder $query) use ($user) {
                    $query->where('outlet_id', $user->outlet_id);
                });
            } else {
                // If a non-Super Admin user has no outlet_id, they shouldn't see any products
                $query->whereRaw('0 = 1'); // Return an empty query
            }
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}