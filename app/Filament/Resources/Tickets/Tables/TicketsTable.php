<?php

namespace App\Filament\Resources\Tickets\Tables;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')
                    ->label('Nº')
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('titulo')
                    ->label('Assunto')
                    ->limit(45)
                    ->searchable()
                    ->description(fn (Ticket $r) => Ticket::categoriaLabels()[$r->categoria] ?? ''),

                TextColumn::make('user.razao_social')
                    ->label('Cliente')
                    ->description(fn (Ticket $r) => $r->user?->email)
                    ->searchable(),

                TextColumn::make('prioridade')
                    ->label('Prioridade')
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->color(fn ($state) => Ticket::prioridadeColors()[$state] ?? 'gray'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Ticket::statusLabels()[$state] ?? $state)
                    ->color(fn ($state) => Ticket::statusColors()[$state] ?? 'gray'),

                TextColumn::make('atribuidoA.name')
                    ->label('Agente')
                    ->placeholder('Não atribuído')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('prazo_resposta')
                    ->label('SLA')
                    ->dateTime('d/m H:i')
                    ->color(fn (Ticket $r) => $r->estaAtrasado() ? 'danger' : 'gray')
                    ->description(fn (Ticket $r) => $r->estaAtrasado() ? '🔴 Atrasado' : null),

                TextColumn::make('created_at')
                    ->label('Aberto')
                    ->since()
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Ticket::statusLabels()),

                SelectFilter::make('prioridade')
                    ->label('Prioridade')
                    ->options([
                        'baixa' => 'Baixa', 'media' => 'Média',
                        'alta' => 'Alta', 'urgente' => 'Urgente',
                    ]),

                SelectFilter::make('categoria')
                    ->label('Categoria')
                    ->options(Ticket::categoriaLabels()),
            ])

            ->recordActions([
                // ─── Responder diretamente da tabela ─────────────────
                Action::make('responder')
                    ->label('Responder')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('warning')
                    ->modalHeading('Responder Chamado')
                    ->modalWidth('2xl')
                    ->form([
                        Textarea::make('mensagem')
                            ->label('Resposta')
                            ->rows(4)
                            ->required(),

                        Select::make('novo_status')
                            ->label('Alterar status para')
                            ->options(Ticket::statusLabels())
                            ->placeholder('Manter status atual'),

                        Toggle::make('is_internal')
                            ->label('Nota interna (não visível ao cliente)')
                            ->default(false),
                    ])
                    ->visible(fn (Ticket $r) => ! in_array($r->status, ['resolvido', 'fechado']))
                    ->action(function (Ticket $record, array $data) {
                        // Criar mensagem
                        $message = TicketMessage::create([
                            'ticket_id'   => $record->id,
                            'user_id'     => auth()->id(),
                            'mensagem'    => $data['mensagem'],
                            'is_internal' => $data['is_internal'] ?? false,
                            'is_admin'    => true,
                        ]);

                        // Alterar status se solicitado
                        $updates = [];
                        if ($data['novo_status'] ?? null) {
                            $updates['status'] = $data['novo_status'];
                            if ($data['novo_status'] === 'resolvido') {
                                $updates['resolvido_em'] = now();
                            }
                        } elseif ($record->status === 'aberto') {
                            $updates['status'] = 'em_atendimento';
                        }

                        if (! empty($updates)) {
                            $record->update($updates);
                        }

                        // Notificar cliente via WhatsApp (apenas se não for nota interna)
                        if (! ($data['is_internal'] ?? false)) {
                            self::notificarClienteWhatsApp($record, $data['mensagem']);
                        }

                        Notification::make()
                            ->title('Resposta enviada com sucesso!')
                            ->success()->send();
                    }),

                // ─── Atribuir agente ──────────────────────────────────
                Action::make('atribuir')
                    ->label('Atribuir')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->form([
                        Select::make('atribuido_a')
                            ->label('Atribuir para')
                            ->options(User::where('is_admin', true)->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(function (Ticket $record, array $data) {
                        $record->update([
                            'atribuido_a' => $data['atribuido_a'],
                            'atribuido_em'=> now(),
                            'status'      => $record->status === 'aberto' ? 'em_atendimento' : $record->status,
                        ]);
                        Notification::make()->title('Chamado atribuído!')->success()->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])

            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    private static function notificarClienteWhatsApp(Ticket $ticket, string $mensagem): void
    {
        try {
            $user = $ticket->user;
            if (! $user?->phone) return;

            $instance = \App\Models\EvolutionInstance::getPadrao();
            if (! $instance) return;

            $phone = preg_replace('/\D/', '', $user->phone);
            if (strlen($phone) <= 11) $phone = '55' . $phone;

            $nome   = $user->razao_social ?? $user->nome_fantasia ?? $user->name;
            $numero = $ticket->numero;
            $texto  = "💬 *Portal Elite Repasse — Suporte*\n\nOlá, {$nome}!\n\nSeu chamado *{$numero}* recebeu uma nova resposta:\n\n_{$mensagem}_\n\n👉 Acesse o portal para ver detalhes e responder.";

            $instance->sendText($phone, $texto);
        } catch (\Exception $e) {
            \Log::warning('TicketsTable: Falha ao enviar WhatsApp', ['error' => $e->getMessage()]);
        }
    }
}
