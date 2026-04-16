<?php

namespace App\Filament\Resources\LandingSettings;

use App\Filament\Resources\LandingSettings\Pages\CreateLandingSetting;
use App\Filament\Resources\LandingSettings\Pages\EditLandingSetting;
use App\Filament\Resources\LandingSettings\Pages\ListLandingSettings;
use App\Filament\Resources\LandingSettings\Schemas\LandingSettingForm;
use App\Filament\Resources\LandingSettings\Tables\LandingSettingsTable;
use App\Models\LandingSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LandingSettingResource extends Resource
{
    protected static ?string $model = LandingSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Configurações da Landing Page';

    protected static ?string $modelLabel = 'Configuração da Landing Page';

    protected static ?string $pluralModelLabel = 'Configurações da Landing Page';

    protected static ?string $navigationGroup = 'Configurações';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return \App\Models\LandingSetting::count() === 0;
    }

    public static function form(Schema $schema): Schema
    {
        return LandingSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LandingSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLandingSettings::route('/'),
            'create' => CreateLandingSetting::route('/create'),
            'edit' => EditLandingSetting::route('/{record}/edit'),
        ];
    }
}
