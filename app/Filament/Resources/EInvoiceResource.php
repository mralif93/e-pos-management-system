<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EInvoiceResource\Pages;
use App\Filament\Resources\EInvoiceResource\RelationManagers;
use App\Models\EInvoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EInvoiceResource extends Resource
{
    protected static ?string $model = EInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'LHDN e-Invoice';

    protected static ?string $navigationLabel = 'e-Invoices';

    protected static ?string $pluralModelLabel = 'e-Invoices';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('sale_id')
                            ->relationship('sale', 'id')
                            ->required(),
                        Forms\Components\TextInput::make('lhdn_invoice_id')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('xml_path')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('qr_code_path')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sale.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lhdn_invoice_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('xml_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('qr_code_path')
                    ->searchable(),
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
                //
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
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEInvoices::route('/'),
            'create' => Pages\CreateEInvoice::route('/create'),
            'edit' => Pages\EditEInvoice::route('/{record}/edit'),
        ];
    }    
}
