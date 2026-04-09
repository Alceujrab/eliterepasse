<?php

namespace App\Filament\Resources\GeneralDocuments\Schemas;

use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Toggle;
use Filament\Schemas\Schema;

class GeneralDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('description')
                    ->default(null),
                TextInput::make('file_path')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
