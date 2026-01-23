<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
                ->submit('create')
                ->color('primary'),
            Action::make('createAndCreateAnother')
                ->label(__('filament-panels::resources/pages/create-record.form.actions.create_another.label'))
                ->submit('createAndCreateAnother')
                ->color('primary'),
            Action::make('cancel')
                ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
