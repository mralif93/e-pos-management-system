<?php

namespace App\Filament\Resources\EInvoiceResource\Pages;

use App\Filament\Resources\EInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEInvoice extends EditRecord
{
    protected static string $resource = EInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
