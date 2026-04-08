<?php

namespace App\Filament\Resources\EvolutionInstances\Pages;

use App\Filament\Resources\EvolutionInstances\EvolutionInstanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEvolutionInstances extends ListRecords
{
    protected static string $resource = EvolutionInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
