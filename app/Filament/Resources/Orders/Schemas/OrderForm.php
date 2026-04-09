<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // ─── Dados da Compra ──────────────────────────────────────
            Section::make('Dados da Compra')
                ->schema([
                    Select::make('user_id')
                        ->label('Cliente (Lojista)')
                        ->options(
                            User::where('is_admin', false)->where('status', 'ativo')
                                ->get()
                                ->mapWithKeys(fn ($u) => [
                                    $u->id => ($u->razao_social ?? $u->nome_fantasia ?? $u->name) . ' — ' . ($u->cnpj ?? $u->email)
                                ])
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('vehicle_id')
                        ->label('Veículo')
                        ->options(
                            Vehicle::where('status', 'available')
                                ->get()
                                ->mapWithKeys(fn ($v) => [
                                    $v->id => "[{$v->plate}] {$v->brand} {$v->model} {$v->model_year} — R$ " . number_format((float) $v->sale_price, 2, ',', '.')
                                ])
                        )
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $vehicle = Vehicle::find($state);
                                if ($vehicle) {
                                    $set('valor_compra', $vehicle->sale_price);
                                    $set('valor_fipe', $vehicle->fipe_price ?? null);
                                }
                            }
                        }),

                    TextInput::make('valor_compra')
                        ->label('Valor de Compra (R$)')
                        ->numeric()
                        ->prefix('R$')
                        ->required(),

                    TextInput::make('valor_fipe')
                        ->label('Valor FIPE (R$)')
                        ->numeric()
                        ->prefix('R$')
                        ->helperText('Preenchido automaticamente ao selecionar o veículo'),

                    Select::make('status')
                        ->label('Status da Compra')
                        ->options(\App\Models\Order::statusLabels())
                        ->default('pendente')
                        ->required(),
                ])->columns(2),

            // ─── Forma de Pagamento (dinâmico via Alpine) ─────────────
            Section::make('Forma de Pagamento')
                ->schema([
                    Select::make('payment_method_id')
                        ->label('Método de Pagamento')
                        ->options(
                            PaymentMethod::ativo()->get()
                                ->mapWithKeys(fn ($m) => [$m->id => $m->nome])
                        )
                        ->required()
                        ->reactive()
                        ->live(),

                    // ─ PIX ─
                    TextInput::make('dados_pix_chave')
                        ->label('Chave PIX')
                        ->visible(fn ($get) => self::isTipo($get('payment_method_id'), 'pix'))
                        ->placeholder('CPF, CNPJ, E-mail ou celular'),

                    // ─ Cartão ─
                    Select::make('dados_cartao_bandeira')
                        ->label('Bandeira')
                        ->options(['Visa' => 'Visa', 'Mastercard' => 'Mastercard', 'Elo' => 'Elo', 'Amex' => 'Amex'])
                        ->visible(fn ($get) => self::isTipo($get('payment_method_id'), 'cartao')),

                    TextInput::make('dados_cartao_parcelas')
                        ->label('Número de Parcelas')
                        ->numeric()
                        ->minValue(1)->maxValue(60)
                        ->visible(fn ($get) => self::isTipo($get('payment_method_id'), 'cartao')),

                    // ─ Boleto ─
                    DateTimePicker::make('dados_boleto_vencimento')
                        ->label('Vencimento do Boleto')
                        ->displayFormat('d/m/Y')
                        ->visible(fn ($get) => self::isTipo($get('payment_method_id'), 'boleto')),

                    // ─ Financiamento ─
                    TextInput::make('dados_financ_banco')
                        ->label('Banco Financiador')
                        ->visible(fn ($get) => self::isTipo($get('payment_method_id'), 'financiamento')),

                    TextInput::make('dados_financ_prazo')
                        ->label('Prazo (meses)')
                        ->numeric()
                        ->visible(fn ($get) => self::isTipo($get('payment_method_id'), 'financiamento')),

                    TextInput::make('dados_financ_taxa')
                        ->label('Taxa de Juros (% a.m.)')
                        ->numeric()
                        ->suffix('%')
                        ->visible(fn ($get) => self::isTipo($get('payment_method_id'), 'financiamento')),

                    // ─ TED ─
                    TextInput::make('dados_ted_banco')
                        ->label('Banco')
                        ->visible(fn ($get) => self::isTipo($get('payment_method_id'), 'ted')),

                    TextInput::make('dados_ted_agencia')
                        ->label('Agência')
                        ->visible(fn ($get) => self::isTipo($get('payment_method_id'), 'ted')),

                    TextInput::make('dados_ted_conta')
                        ->label('Conta')
                        ->visible(fn ($get) => self::isTipo($get('payment_method_id'), 'ted')),
                ])->columns(2),

            // ─── Observações ──────────────────────────────────────────
            Section::make('Observações')
                ->schema([
                    Textarea::make('observacoes')
                        ->label('Observações Internas')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    private static function isTipo(?int $paymentMethodId, string $tipo): bool
    {
        if (! $paymentMethodId) return false;
        $method = PaymentMethod::find($paymentMethodId);
        return $method?->tipo === $tipo;
    }
}
