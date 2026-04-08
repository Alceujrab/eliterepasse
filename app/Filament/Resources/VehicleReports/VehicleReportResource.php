<?php

namespace App\Filament\Resources\VehicleReports;

use App\Filament\Resources\VehicleReports\Pages\CreateVehicleReport;
use App\Filament\Resources\VehicleReports\Pages\EditVehicleReport;
use App\Filament\Resources\VehicleReports\Pages\ListVehicleReports;
use App\Filament\Resources\VehicleReports\Pages\ViewVehicleReport;
use App\Filament\Resources\VehicleReports\Schemas\VehicleReportForm;
use App\Filament\Resources\VehicleReports\Schemas\VehicleReportInfolist;
use App\Filament\Resources\VehicleReports\Tables\VehicleReportsTable;
use App\Models\VehicleReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleReportResource extends Resource
{
    protected static ?string $model = VehicleReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Estoque';

    protected static ?string $navigationLabel = 'Laudos e Vistorias';

    protected static ?string $modelLabel = 'Laudo';

    protected static ?string $pluralModelLabel = 'Laudos';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'numero';

    /** Badge com laudos em revisão */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->where('status', 'em_revisao')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return VehicleReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VehicleReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListVehicleReports::route('/'),
            'create' => CreateVehicleReport::route('/create'),
            'view'   => ViewVehicleReport::route('/{record}'),
            'edit'   => EditVehicleReport::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
