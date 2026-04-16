<?php

namespace App\Filament\Resources\LandingBanners\Pages;

use App\Filament\Resources\LandingBanners\LandingBannerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLandingBanners extends ListRecords
{
    protected static string $resource = LandingBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
