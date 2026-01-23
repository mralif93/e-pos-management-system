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
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Product Management';

    public static function canView(Model $record = null): bool
    {
        return in_array(auth()->user()->role, ['Admin', 'Manager']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Details')
                    ->description('Manage product information.')
                    ->schema([
                        Select::make('category_id')
                            ->label('Category')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->prefix('$'),
                        TextInput::make('cost')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->prefix('$'),
                        TextInput::make('stock_level')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_level')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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

        $selectedOutletId = null;

        if (auth()->user()->role === 'Admin') {
            if (Session::has('selected_admin_outlet_id') && Session::get('selected_admin_outlet_id') !== null) {
                $selectedOutletId = Session::get('selected_admin_outlet_id');
            }
        } elseif (auth()->user()->outlet_id) {
            $selectedOutletId = auth()->user()->outlet_id;
        }

        if ($selectedOutletId !== null) {
            $query->whereHas('outlets', function (Builder $query) use ($selectedOutletId) {
                $query->where('outlet_id', $selectedOutletId);
            });
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProductOutletsRelationManager::class,
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
