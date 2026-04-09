<?php

namespace App\Filament\Resources\Contracts\Schemas;

use App\Models\Contract;
use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─── Identificação ────────────────────────────────────
                Section::make('Identificação')
                    ->schema([
                        TextInput::make('numero')
                            ->label('Nº Contrato')
                            ->default(fn () => Contract::gerarNumero())
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('template')
                            ->label('Template')
                            ->options([
                                'padrao' => 'Padrão',
                            ])
                            ->default('padrao')
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options(Contract::statusLabels())
                            ->default('rascunho')
                            ->required(),
                    ])->columns(3),

                // ─── Partes do Contrato ───────────────────────────────
                Section::make('Partes do Contrato')
                    ->schema([
                        Select::make('order_id')
                            ->label('Pedido de Compra (opcional)')
                            ->options(
                                Order::with('user', 'vehicle')
                                    ->latest()
                                    ->limit(100)
                                    ->get()
                                    ->mapWithKeys(fn ($o) => [
                                        $o->id => "{$o->numero} — " . ($o->user?->razao_social ?? $o->user?->name ?? '?'),
                                    ])
                            )
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $order = Order::find($state);
                                    if ($order) {
                                        $set('user_id', $order->user_id);
                                        $set('vehicle_id', $order->vehicle_id);
                                        $set('valor_contrato', $order->valor_compra);
                                    }
                                }
                            }),

                        Select::make('user_id')
                            ->label('Comprador (Lojista)')
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
                            ->label('Veículo')
                            ->options(
                                Vehicle::all()
                                    ->mapWithKeys(fn ($v) => [
                                        $v->id => "[{$v->plate}] {$v->brand} {$v->model} {$v->model_year}",
                                    ])
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                // ─── Valores ──────────────────────────────────────────
                Section::make('Valores')
                    ->schema([
                        TextInput::make('valor_contrato')
                            ->label('Valor do Contrato (R$)')
                            ->numeric()
                            ->prefix('R$')
                            ->required(),

                        TextInput::make('forma_pagamento')
                            ->label('Forma de Pagamento')
                            ->placeholder('Ex: À vista, Financiamento, etc.'),
                    ])->columns(2),
            ]);
    }
}
