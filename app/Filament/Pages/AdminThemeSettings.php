<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class AdminThemeSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static string $view = 'filament.pages.admin-theme-settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $title = 'Theme';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'theme_color' => Auth::user()->theme_color ?? 'indigo',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dashboard Theme')
                    ->description('Select your preferred color scheme for the admin panel.')
                    ->schema([
                        Select::make('theme_color')
                            ->label('Primary Color')
                            ->options([
                                'indigo' => 'Indigo (Default)',
                                'blue' => 'Blue',
                                'red' => 'Red',
                                'green' => 'Green',
                                'teal' => 'Teal',
                                'orange' => 'Orange',
                                'purple' => 'Purple',
                                'pink' => 'Pink',
                                'slate' => 'Slate',
                                'amber' => 'Amber',
                            ])
                            ->required()
                            ->selectablePlaceholder(false),
                    ])->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = Auth::user();
        $user->theme_color = $data['theme_color'];
        $user->save();

        Notification::make()
            ->title('Theme Updated')
            ->success()
            ->body('Your dashboard theme has been updated. Refresh to see changes.')
            ->send();

        // Optional: Force a redirect to refresh styles immediately
        $this->redirect(request()->header('Referer'));
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }
}
