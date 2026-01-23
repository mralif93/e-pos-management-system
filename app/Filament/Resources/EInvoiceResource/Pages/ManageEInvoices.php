<?php

namespace App\Filament\Resources\EInvoiceResource\Pages;

use App\Filament\Resources\EInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEInvoices extends ManageRecords
{
    protected static string $resource = EInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
