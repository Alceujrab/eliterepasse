<?php

namespace App\Filament\Resources\EvolutionInstances;

use App\Models\EvolutionInstance;
use App\Services\EvolutionService;
use BackedEnum;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvolutionInstanceResource extends Resource
{
    protected static ?string $model = EvolutionInstance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|\UnitEnum|null $navigationGroup = 'Comunicação';

    protected static ?string $navigationLabel = 'WhatsApp — Instâncias';

    protected static ?string $modelLabel = 'Instância WhatsApp';

    protected static ?string $pluralModelLabel = 'Instâncias WhatsApp';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Identificação')
                ->columns(2)
                ->schema([
                    TextInput::make('nome')
                        ->label('Nome da Instância')
                        ->required()
                        ->placeholder('Ex: Principal, Suporte, Vendas'),

                    TextInput::make('instancia')
                        ->label('ID da Instância (Evolution)')
                        ->required()
                        ->placeholder('Ex: elite-repasse')
                        ->helperText('Exatamente como cadastrado no painel Evolution.'),
                ]),

            Section::make('Conexão com a API')
                ->columns(1)
                ->schema([
                    TextInput::make('url_base')
                        ->label('URL Base da API')
                        ->required()
                        ->url()
                        ->placeholder('https://api.auto.inf.br')
                        ->helperText('Sem barra no final.'),

                    TextInput::make('api_key')
                        ->label('API Key (Global ou da Instância)')
                        ->required()
                        ->password()
                        ->revealable(),
                ]),

            Section::make('Configurações')
                ->columns(2)
                ->schema([
                    Toggle::make('ativo')
                        ->label('Instância Ativa')
                        ->default(true),

                    Toggle::make('padrao')
                        ->label('Instância Padrão do Sistema')
                        ->helperText('Apenas uma instância pode ser padrão.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('instancia')
                    ->label('ID Evolution')
                    ->fontFamily('mono')
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('url_base')
                    ->label('URL Base')
                    ->limit(35)
                    ->tooltip(fn ($state) => $state),

                TextColumn::make('status_conexao')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'open'       => '🟢 Conectado',
                        'connecting' => '🟡 Conectando',
                        'close'      => '🔴 Desconectado',
                        default      => '⚪ ' . ($state ?? 'Não verificado'),
                    })
                    ->color(fn ($state) => match($state) {
                        'open'  => 'success',
                        'close' => 'danger',
                        default => 'warning',
                    }),

                IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),

                IconColumn::make('padrao')
                    ->label('Padrão')
                    ->boolean(),

                TextColumn::make('verificado_em')
                    ->label('Último Teste')
                    ->since()
                    ->placeholder('Nunca testado'),
            ])
            ->recordActions([
                // Verificar Conexão
                Action::make('verificar')
                    ->label('Testar Conexão')
                    ->icon('heroicon-o-wifi')
                    ->color('info')
                    ->action(function (EvolutionInstance $record) {
                        $ok = $record->testarConexao();
                        if ($ok) {
                            Notification::make()->title('✅ Instância conectada!')->success()->send();
                        } else {
                            Notification::make()->title('❌ Falha na conexão. Verifique os dados.')->danger()->send();
                        }
                    }),

                // QR Code
                Action::make('qr_code')
                    ->label('Ver QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('warning')
                    ->modalHeading(fn (EvolutionInstance $r) => "QR Code — {$r->nome}")
                    ->modalContent(function (EvolutionInstance $record) {
                        $base64 = $record->getQrCode();

                        if (! $base64) {
                            return new \Illuminate\Support\HtmlString(
                                '<p class="text-center text-gray-500 py-8">
                                    Não foi possível obter o QR Code.<br>
                                    Verifique se a instância está desconectada.
                                </p>'
                            );
                        }

                        $src = str_starts_with($base64, 'data:') ? $base64 : "data:image/png;base64,{$base64}";

                        return new \Illuminate\Support\HtmlString(
                            "<div class='flex flex-col items-center gap-4 py-4'>
                                <img src='{$src}' class='w-64 h-64 rounded-xl shadow-lg' alt='QR Code WhatsApp' />
                                <p class='text-sm text-gray-500 text-center'>
                                    Abra o WhatsApp → Dispositivos Vinculados → Vincular dispositivo<br>
                                    e escaneie o QR Code acima.
                                </p>
                                <p class='text-xs text-gray-400'>O QR Code expira em 40 segundos. Se vencer, feche e abra novamente.</p>
                            </div>"
                        );
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar'),

                // Enviar Teste
                Action::make('enviar_teste')
                    ->label('Enviar Teste')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->form([
                        TextInput::make('telefone')
                            ->label('Número (com DDD, sem +55)')
                            ->required()
                            ->placeholder('11987654321'),
                    ])
                    ->action(function (EvolutionInstance $record, array $data) {
                        $result = $record->sendText(
                            $data['telefone'],
                            "🧪 *Teste de Integração — Elite Repasse*\n\nEsta é uma mensagem de teste do painel admin.\n\n_Instância: {$record->nome}_"
                        );

                        if ($result['success']) {
                            Notification::make()->title('✅ Mensagem enviada com sucesso!')->success()->send();
                        } else {
                            Notification::make()->title('❌ Falha ao enviar: ' . ($result['error'] ?? 'Erro desconhecido'))->danger()->send();
                        }
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('padrao', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEvolutionInstances::route('/'),
            'create' => Pages\CreateEvolutionInstance::route('/create'),
            'edit'   => Pages\EditEvolutionInstance::route('/{record}/edit'),
        ];
    }
}
