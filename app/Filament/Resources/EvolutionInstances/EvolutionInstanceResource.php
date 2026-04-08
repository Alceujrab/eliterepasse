<?php

namespace App\Filament\Resources\EvolutionInstances;

use App\Filament\Resources\EvolutionInstances\Pages\CreateEvolutionInstance;
use App\Filament\Resources\EvolutionInstances\Pages\EditEvolutionInstance;
use App\Filament\Resources\EvolutionInstances\Pages\ListEvolutionInstances;
use App\Filament\Resources\EvolutionInstances\Schemas\EvolutionInstanceForm;
use App\Filament\Resources\EvolutionInstances\Tables\EvolutionInstancesTable;
use App\Models\EvolutionInstance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EvolutionInstanceResource extends Resource
{
    protected static ?string $model = EvolutionInstance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|\UnitEnum|null $navigationGroup = 'Configurações';

    protected static ?string $navigationLabel = 'Instâncias WhatsApp';

    protected static ?string $modelLabel = 'Instância WhatsApp';

    protected static ?string $pluralModelLabel = 'Instâncias WhatsApp';

    protected static ?string $recordTitleAttribute = 'nome';

    public static function form(Schema $schema): Schema
    {
        return EvolutionInstanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvolutionInstancesTable::configure($table);
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
            'index' => ListEvolutionInstances::route('/'),
            'create' => CreateEvolutionInstance::route('/create'),
            'edit' => EditEvolutionInstance::route('/{record}/edit'),
        ];
    }
}
