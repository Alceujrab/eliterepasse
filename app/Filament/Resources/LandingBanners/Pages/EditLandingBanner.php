<?php

namespace App\Filament\Resources\LandingBanners\Pages;

use App\Filament\Resources\LandingBanners\LandingBannerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLandingBanner extends EditRecord
{
    protected static string $resource = LandingBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
