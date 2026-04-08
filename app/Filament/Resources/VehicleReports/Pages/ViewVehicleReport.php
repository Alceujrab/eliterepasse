<?php

namespace App\Filament\Resources\VehicleReports\Pages;

use App\Filament\Resources\VehicleReports\VehicleReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVehicleReport extends ViewRecord
{
    protected static string $resource = VehicleReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
