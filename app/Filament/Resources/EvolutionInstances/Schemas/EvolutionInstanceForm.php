<?php

namespace App\Filament\Resources\EvolutionInstances\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EvolutionInstanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->required(),
                TextInput::make('instancia')
                    ->required(),
                TextInput::make('url_base')
                    ->required(),
                Textarea::make('api_key')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('ativo')
                    ->required(),
                Toggle::make('padrao')
                    ->required(),
                TextInput::make('status_conexao')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('verificado_em'),
            ]);
    }
}
