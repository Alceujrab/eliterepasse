<?php

namespace App\Filament\Resources\GeneralDocuments;

use App\Filament\Resources\GeneralDocuments\Pages\CreateGeneralDocument;
use App\Filament\Resources\GeneralDocuments\Pages\EditGeneralDocument;
use App\Filament\Resources\GeneralDocuments\Pages\ListGeneralDocuments;
use App\Filament\Resources\GeneralDocuments\Schemas\GeneralDocumentForm;
use App\Filament\Resources\GeneralDocuments\Tables\GeneralDocumentsTable;
use App\Models\GeneralDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GeneralDocumentResource extends Resource
{
    protected static ?string $model = GeneralDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Documentos Gerais';

    protected static ?string $modelLabel = 'Documento Geral';

    protected static ?string $pluralModelLabel = 'Documentos Gerais';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return GeneralDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GeneralDocumentsTable::configure($table);
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
            'index' => ListGeneralDocuments::route('/'),
            'create' => CreateGeneralDocument::route('/create'),
            'edit' => EditGeneralDocument::route('/{record}/edit'),
        ];
    }
}
