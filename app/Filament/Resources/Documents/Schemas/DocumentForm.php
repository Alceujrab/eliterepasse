<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('vehicle_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('title')
                    ->required(),
                TextInput::make('file_path')
                    ->required(),
            ]);
    }
}
