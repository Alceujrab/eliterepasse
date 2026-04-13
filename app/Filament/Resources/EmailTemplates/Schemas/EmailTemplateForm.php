<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Identificação')
                ->schema([
                    TextInput::make('nome')
                        ->label('Nome do Template')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('slug')
                        ->label('Slug (identificador)')
                        ->disabled()
                        ->dehydrated(false),

                    Toggle::make('ativo')
                        ->label('Template ativo')
                        ->helperText('Se desativado, o sistema usará o conteúdo padrão hardcoded.'),
                ])->columns(2),

            Section::make('Conteúdo do E-mail')
                ->schema([
                    TextInput::make('assunto')
                        ->label('Assunto')
                        ->required()
                        ->helperText('Suporta variáveis: {{nome}}, {{numero}}, etc.'),

                    TextInput::make('saudacao')
                        ->label('Saudação')
                        ->helperText('Ex: Olá, {{nome}}!'),

                    Textarea::make('corpo')
                        ->label('Corpo do E-mail')
                        ->required()
                        ->rows(10)
                        ->helperText('Cada linha vira um parágrafo. Suporta **negrito** (Markdown). Use variáveis com {{variavel}}.'),

                    TextInput::make('texto_acao')
                        ->label('Texto do Botão'),

                    TextInput::make('url_acao')
                        ->label('URL do Botão')
                        ->helperText('Suporta {{portal_url}} para URL dinâmica.'),

                    Textarea::make('texto_rodape')
                        ->label('Texto após o botão')
                        ->rows(3),
                ]),

            Section::make('Variáveis Disponíveis')
                ->schema([
                    Placeholder::make('variaveis_info')
                        ->label('')
                        ->content(fn ($record) => $record
                            ? 'Variáveis: ' . collect($record->variaveis_disponiveis)->map(fn ($v) => "{{" . $v . "}}")->implode(', ')
                            : '—'
                        ),
                ]),
        ]);
    }
}
