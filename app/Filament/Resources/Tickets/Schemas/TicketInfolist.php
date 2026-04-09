<?php

namespace App\Filament\Resources\Tickets\Schemas;

use App\Models\Ticket;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TicketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('numero')
                    ->label('Nº Chamado'),
                TextEntry::make('titulo')
                    ->label('Assunto')
                    ->columnSpanFull(),
                TextEntry::make('categoria')
                    ->label('Categoria')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Ticket::categoriaLabels()[$state] ?? $state),
                TextEntry::make('prioridade')
                    ->label('Prioridade')
                    ->badge()
                    ->color(fn ($state) => Ticket::prioridadeColors()[$state] ?? 'gray'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Ticket::statusLabels()[$state] ?? $state)
                    ->color(fn ($state) => Ticket::statusColors()[$state] ?? 'gray'),
                TextEntry::make('user.name')
                    ->label('Cliente'),
                TextEntry::make('atribuidoA.name')
                    ->label('Agente Responsável')
                    ->placeholder('Não atribuído'),
                TextEntry::make('vehicle.plate')
                    ->label('Veículo')
                    ->placeholder('—'),
                TextEntry::make('prazo_resposta')
                    ->label('Prazo SLA')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),
                TextEntry::make('resolucao')
                    ->label('Resolução')
                    ->placeholder('—')
                    ->columnSpanFull(),
                TextEntry::make('resolvido_em')
                    ->label('Resolvido em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),
                TextEntry::make('fechado_em')
                    ->label('Fechado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—'),
                TextEntry::make('created_at')
                    ->label('Aberto em')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
