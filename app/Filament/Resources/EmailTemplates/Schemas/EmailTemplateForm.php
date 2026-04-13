<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use App\Services\GeminiService;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
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
                        ->required(fn ($record) => $record === null)
                        ->disabled(fn ($record) => $record !== null)
                        ->dehydrated(fn ($record) => $record === null),

                    TextInput::make('slug')
                        ->label('Slug (identificador)')
                        ->required(fn ($record) => $record === null)
                        ->disabled(fn ($record) => $record !== null)
                        ->dehydrated(fn ($record) => $record === null)
                        ->helperText(fn ($record) => $record === null
                            ? 'Identificador único. Use letras minúsculas, números e underscores.'
                            : null
                        )
                        ->unique(ignoreRecord: true),

                    Toggle::make('ativo')
                        ->label('Template ativo')
                        ->default(true)
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
                        ->helperText('Cada linha vira um parágrafo. Suporta **negrito** (Markdown). Use variáveis com {{variavel}}.')
                        ->hintAction(
                            Action::make('gerar_com_ia')
                                ->label('Gerar com IA')
                                ->icon('heroicon-o-sparkles')
                                ->color('warning')
                                ->form([
                                    TextInput::make('descricao')
                                        ->label('Descreva o e-mail que deseja gerar')
                                        ->required()
                                        ->placeholder('Ex: E-mail de boas-vindas para novo lojista cadastrado'),
                                ])
                                ->action(function (array $data, Set $set, Get $get, $record) {
                                    try {
                                        $variaveis = $record?->variaveis_disponiveis ?? $get('variaveis_disponiveis') ?? [];
                                        $corpo = GeminiService::gerarCorpoEmail($data['descricao'], $variaveis);
                                        $set('corpo', $corpo);

                                        Notification::make()
                                            ->title('Corpo gerado com sucesso!')
                                            ->success()
                                            ->send();
                                    } catch (\Exception $e) {
                                        Notification::make()
                                            ->title('Erro ao gerar com IA')
                                            ->body($e->getMessage())
                                            ->danger()
                                            ->send();
                                    }
                                })
                        ),

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
                        )
                        ->visible(fn ($record) => $record !== null),

                    TagsInput::make('variaveis_disponiveis')
                        ->label('Variáveis Disponíveis')
                        ->helperText('Digite o nome da variável e pressione Enter. Ex: nome, email, valor')
                        ->placeholder('nome_da_variavel')
                        ->visible(fn ($record) => $record === null),
                ]),
        ]);
    }
}
