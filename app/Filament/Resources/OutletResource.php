<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutletResource\Pages;
use App\Filament\Resources\OutletResource\RelationManagers;
use App\Models\Outlet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OutletResource extends Resource
{
    protected static ?string $model = Outlet::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Outlet Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('outlet_code')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                        Forms\Components\Toggle::make('has_pos_access')
                            ->required(),
                    ])->columns(2),
                Forms\Components\Section::make('Business Identity (LHDN)')
                    ->description('Mandatory business details for LHDN e-Invoicing.')
                    ->schema([
                        Forms\Components\TextInput::make('settings.business_name')
                            ->label('Business Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('settings.business_registration_number')
                            ->label('Registration No. (SSM)')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('settings.tax_identification_number')
                            ->label('Tax ID (TIN)')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Textarea::make('settings.business_address')
                            ->label('Business Address')
                            ->required()
                            ->rows(3),
                        Forms\Components\TextInput::make('settings.contact_email')
                            ->label('Contact Email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('settings.contact_phone')
                            ->label('Contact Phone')
                            ->tel(),
                    ])->columns(2),

                Forms\Components\Section::make('Invoice Format & Numbering')
                    ->schema([
                        Forms\Components\TextInput::make('settings.invoice_prefix')
                            ->label('Invoice Prefix')
                            ->default('INV/')
                            ->placeholder('INV/'),
                        Forms\Components\TextInput::make('settings.invoice_number_counter')
                            ->label('Next Invoice Number')
                            ->numeric()
                            ->default(1)
                            ->hint('Auto-increments. Change only to reset/adjust.'),
                        Forms\Components\Toggle::make('settings.reset_counter_monthly')
                            ->label('Reset Counter Monthly')
                            ->default(true),
                        Forms\Components\Toggle::make('settings.show_barcode')
                            ->label('Show Barcode on Receipt')
                            ->default(true),
                        Forms\Components\Toggle::make('settings.show_qr_code')
                            ->label('Show LHDN QR Code')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Tax & Compliance')
                    ->schema([
                        Forms\Components\Select::make('settings.default_tax_type')
                            ->label('Tax Type')
                            ->options([
                                'SST' => 'SST',
                                'GST' => 'GST',
                                'VAT' => 'VAT',
                                'None' => 'None',
                            ])
                            ->default('SST'),
                        Forms\Components\TextInput::make('settings.currency_symbol')
                            ->label('Currency Symbol')
                            ->default('$')
                            ->maxLength(5),
                        Forms\Components\TextInput::make('settings.tax_rate') // Mapped to existing tax_rate logic if any, or new key
                            ->label('Default Tax Rate (%)')
                            ->numeric()
                            ->default(6)
                            ->suffix('%'),
                        Forms\Components\Toggle::make('settings.tax_inclusive_pricing')
                            ->label('Tax Inclusive Pricing')
                            ->default(true),
                        Forms\Components\Toggle::make('settings.auto_submit_e_invoice')
                            ->label('Auto-Submit e-Invoice')
                            ->default(true)
                            ->helperText('Submit to MyInvois immediately after sale.'),
                        Forms\Components\TextInput::make('settings.e_invoice_delay_minutes')
                            ->label('Submission Delay (Minutes)')
                            ->numeric()
                            ->default(5),
                    ])->columns(2),

                Forms\Components\Section::make('Receipt Customization')
                    ->schema([
                        Forms\Components\Textarea::make('settings.receipt_header')
                            ->label('Receipt Header')
                            ->rows(3)
                            ->placeholder("Thank you for your visit!\nOpen daily 8AM–10PM"),
                        Forms\Components\Textarea::make('settings.receipt_footer')
                            ->label('Receipt Footer')
                            ->rows(3)
                            ->placeholder("Follow us on IG @kedai_kopi\ne-Invoice avail at myinvois.hasil.gov.my"),
                        Forms\Components\Toggle::make('settings.show_outlet_name')
                            ->label('Show Outlet Name')
                            ->default(true),
                        Forms\Components\Toggle::make('settings.show_cashier_name')
                            ->label('Show Cashier Name')
                            ->default(true),
                        Forms\Components\FileUpload::make('settings.logo_path')
                            ->label('Receipt Logo')
                            ->image()
                            ->directory('outlet-logos')
                            ->visibility('public'),
                    ])->columns(2),

                Forms\Components\Section::make('MyInvois API Settings')
                    ->description('Credentials for LHDN e-Invoicing Integration.')
                    ->schema([
                        Forms\Components\Select::make('settings.myinvois_environment')
                            ->label('Environment')
                            ->options([
                                'sandbox' => 'Sandbox',
                                'production' => 'Production',
                            ])
                            ->default('sandbox'),
                        Forms\Components\TextInput::make('settings.myinvois_client_id')
                            ->label('Client ID')
                            ->password()
                            ->revealable(),
                        Forms\Components\TextInput::make('settings.myinvois_client_secret')
                            ->label('Client Secret')
                            ->password()
                            ->revealable(),
                        Forms\Components\Toggle::make('settings.auto_retry_failed_submissions')
                            ->label('Auto-Retry Failed Submissions')
                            ->default(true),
                    ])->columns(2)
                    ->collapsed(), // Collapse by default for security/space
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('outlet_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_pos_access')
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListOutlets::route('/'),
            'create' => Pages\CreateOutlet::route('/create'),
            'edit' => Pages\EditOutlet::route('/{record}/edit'),
        ];
    }
}
