<?php

namespace App\Filament\Resources\LandingBanners;

use App\Filament\Resources\LandingBanners\Pages\CreateLandingBanner;
use App\Filament\Resources\LandingBanners\Pages\EditLandingBanner;
use App\Filament\Resources\LandingBanners\Pages\ListLandingBanners;
use App\Filament\Resources\LandingBanners\Schemas\LandingBannerForm;
use App\Filament\Resources\LandingBanners\Tables\LandingBannersTable;
use App\Models\LandingBanner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LandingBannerResource extends Resource
{
    protected static ?string $model = LandingBanner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Banners do Site';

    protected static ?string $modelLabel = 'Banner';

    protected static ?string $pluralModelLabel = 'Banners';

    protected static ?string $navigationGroup = 'Configurações';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return LandingBannerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LandingBannersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLandingBanners::route('/'),
            'create' => CreateLandingBanner::route('/create'),
            'edit' => EditLandingBanner::route('/{record}/edit'),
        ];
    }
}
