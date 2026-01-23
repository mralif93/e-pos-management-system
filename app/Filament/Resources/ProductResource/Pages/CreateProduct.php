<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

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
