<?php

namespace App\Filament\Resources\GeneralDocuments\Pages;

use App\Filament\Resources\GeneralDocuments\GeneralDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGeneralDocuments extends ListRecords
{
    protected static string $resource = GeneralDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
