<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\Outlet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Support\Facades\Session;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users'; // Changed

    protected static ?string $navigationGroup = 'User Management'; // Added

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Details')
                    ->description('Manage user information and roles.')
                    ->schema([
                        Select::make('outlet_id')
                            ->label('Outlet')
                            ->options(Outlet::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(fn(string $context): bool => $context === 'create'),
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At')
                            ->readOnly(),
                        TextInput::make('password')
                            ->password()
                            ->required(fn(string $context): bool => $context === 'create')
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => bcrypt($state)),
                        Select::make('role')
                            ->options([
                                'Admin' => 'Admin',
                                'Manager' => 'Manager',
                                'Cashier' => 'Cashier',
                                'Viewer' => 'Viewer',
                            ])
                            ->required(),
                        Toggle::make('is_active')
                            ->required(),
                        TextInput::make('staff_id'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('outlet.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('role')
                    ->searchable(),
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
                SelectFilter::make('role')
                    ->options([
                        'Admin' => 'Admin',
                        'Manager' => 'Manager',
                        'Cashier' => 'Cashier',
                        'Viewer' => 'Viewer',
                    ]),
                TernaryFilter::make('is_active'),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->label(false)
                    ->tooltip('Edit'),
                DeleteAction::make()
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
                $query->where('outlet_id', $selectedOutletId);
            }
            // If no outlet is selected for Super Admin, they see all users by default.
        } else {
            // For all other roles, filter by their assigned outlet.
            if ($user->outlet_id) {
                $query->where('outlet_id', $user->outlet_id);
            } else {
                // If a non-Super Admin user has no outlet_id, they shouldn't see any users
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}