<?php

namespace App\Filament\Resources\Tickets\Schemas;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─── Identificação ────────────────────────────────────
                Section::make('Identificação')
                    ->schema([
                        TextInput::make('titulo')
                            ->label('Assunto')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Descreva brevemente o problema')
                            ->columnSpanFull(),

                        Select::make('categoria')
                            ->label('Categoria')
                            ->options(Ticket::categoriaLabels())
                            ->default('duvida')
                            ->required(),

                        Select::make('prioridade')
                            ->label('Prioridade')
                            ->options([
                                'baixa'   => 'Baixa',
                                'media'   => 'Média',
                                'alta'    => 'Alta',
                                'urgente' => 'Urgente',
                            ])
                            ->default('media')
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options(Ticket::statusLabels())
                            ->default('aberto')
                            ->required(),
                    ])->columns(3),

                // ─── Vinculação ───────────────────────────────────────
                Section::make('Vinculação')
                    ->schema([
                        Select::make('user_id')
                            ->label('Cliente (Lojista)')
                            ->options(
                                User::where('is_admin', false)
                                    ->get()
                                    ->mapWithKeys(fn ($u) => [
                                        $u->id => ($u->razao_social ?? $u->nome_fantasia ?? $u->name) . ' — ' . ($u->cnpj ?? $u->email),
                                    ])
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('vehicle_id')
                            ->label('Veículo (opcional)')
                            ->options(
                                Vehicle::all()
                                    ->mapWithKeys(fn ($v) => [
                                        $v->id => "[{$v->plate}] {$v->brand} {$v->model} {$v->model_year}",
                                    ])
                            )
                            ->searchable()
                            ->preload(),

                        Select::make('order_id')
                            ->label('Pedido (opcional)')
                            ->options(
                                Order::with('user')
                                    ->latest()
                                    ->limit(100)
                                    ->get()
                                    ->mapWithKeys(fn ($o) => [
                                        $o->id => "{$o->numero} — " . ($o->user?->razao_social ?? $o->user?->name ?? '?'),
                                    ])
                            )
                            ->searchable()
                            ->preload(),

                        Select::make('atribuido_a')
                            ->label('Agente Responsável')
                            ->options(
                                User::where('is_admin', true)
                                    ->get()
                                    ->mapWithKeys(fn ($u) => [
                                        $u->id => $u->name . ' — ' . $u->email,
                                    ])
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Não atribuído'),
                    ])->columns(2),
            ]);
    }
}
