<?php

namespace App\Filament\Resources\EvolutionInstances\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class EvolutionInstanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identificação da Instância')
                    ->description('Configure a conexão com o servidor Evolution GO.')
                    ->schema([
                        TextInput::make('nome')
                            ->label('Nome da Instância')
                            ->placeholder('Ex: Principal, Vendas, Suporte')
                            ->required(),

                        TextInput::make('instancia')
                            ->label('Nome Técnico da Instância')
                            ->placeholder('Ex: eliterepasse (conforme cadastrado no Evolution GO)')
                            ->required()
                            ->helperText('Exatamente como aparece no painel em api.auto.inf.br'),

                        TextInput::make('url_base')
                            ->label('URL Base do Servidor')
                            ->placeholder('https://api.auto.inf.br')
                            ->required()
                            ->url()
                            ->default('https://api.auto.inf.br'),

                        TextInput::make('api_key')
                            ->label('Token de Autenticação (API Key)')
                            ->placeholder('Bearer token da instância')
                            ->required()
                            ->password()
                            ->revealable(),
                    ])->columns(2),

                Section::make('Status e Configurações')
                    ->schema([
                        Toggle::make('ativo')
                            ->label('Instância Ativa')
                            ->default(true),

                        Toggle::make('padrao')
                            ->label('Instância Padrão do Sistema')
                            ->helperText('Será usada por padrão em todas as notificações automáticas')
                            ->default(false),
                    ])->columns(2),
            ]);
    }
}
