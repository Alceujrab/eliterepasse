<?php

namespace App\Filament\Resources\EvolutionInstances\Pages;

use App\Filament\Resources\EvolutionInstances\EvolutionInstanceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEvolutionInstance extends EditRecord
{
    protected static string $resource = EvolutionInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
