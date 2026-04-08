<?php

namespace App\Filament\Resources\VehicleReports\Pages;

use App\Filament\Resources\VehicleReports\VehicleReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicleReports extends ListRecords
{
    protected static string $resource = VehicleReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
