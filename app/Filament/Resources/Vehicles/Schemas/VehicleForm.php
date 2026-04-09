<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─── Coluna Principal (2/3) ──────────────────────────────────
                Group::make()->schema([

                    // ── Integração FIPE ───────────────────────────────────────
                    Section::make('🔍 Integração FIPE Automática')
                        ->description('Selecione Marca → Modelo → Ano para preencher os dados da tabela FIPE automaticamente.')
                        ->schema([
                            Select::make('fipe_brand_id')
                                ->label('Marca (FIPE)')
                                ->options(function () {
                                    return cache()->remember('fipe.brands', 86400, function () {
                                        $r = Http::timeout(5)->get('https://parallelum.com.br/fipe/api/v1/carros/marcas');
                                        return $r->successful()
                                            ? collect($r->json())->pluck('nome', 'codigo')->toArray()
                                            : [];
                                    });
                                })
                                ->searchable()
                                ->live()
                                ->dehydrated(false)
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $set('fipe_model_id', null);
                                    $set('fipe_year_id', null);
                                    $brands = cache('fipe.brands', []);
                                    if (isset($brands[$state])) {
                                        $set('brand', $brands[$state]);
                                    }
                                }),

                            Select::make('fipe_model_id')
                                ->label('Modelo (FIPE)')
                                ->options(function (Get $get) {
                                    $brand = $get('fipe_brand_id');
                                    if (! $brand) return [];
                                    return cache()->remember("fipe.models.{$brand}", 86400, function () use ($brand) {
                                        $r = Http::timeout(5)->get("https://parallelum.com.br/fipe/api/v1/carros/marcas/{$brand}/modelos");
                                        return $r->successful()
                                            ? collect($r->json()['modelos'])->pluck('nome', 'codigo')->toArray()
                                            : [];
                                    });
                                })
                                ->searchable()
                                ->live()
                                ->dehydrated(false)
                                ->disabled(fn (Get $get) => empty($get('fipe_brand_id')))
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    $set('fipe_year_id', null);
                                    $brand = $get('fipe_brand_id');
                                    $models = cache("fipe.models.{$brand}", []);
                                    if (isset($models[$state])) {
                                        $set('model', $models[$state]);
                                    }
                                }),

                            Select::make('fipe_year_id')
                                ->label('Ano / Emissão (FIPE)')
                                ->options(function (Get $get) {
                                    $brand = $get('fipe_brand_id');
                                    $model = $get('fipe_model_id');
                                    if (! $brand || ! $model) return [];
                                    $r = Http::timeout(5)->get("https://parallelum.com.br/fipe/api/v1/carros/marcas/{$brand}/modelos/{$model}/anos");
                                    return $r->successful()
                                        ? collect($r->json())->pluck('nome', 'codigo')->toArray()
                                        : [];
                                })
                                ->searchable()
                                ->live()
                                ->dehydrated(false)
                                ->disabled(fn (Get $get) => empty($get('fipe_model_id')))
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    $brand = $get('fipe_brand_id');
                                    $model = $get('fipe_model_id');
                                    if ($brand && $model && $state) {
                                        $r = Http::timeout(5)->get("https://parallelum.com.br/fipe/api/v1/carros/marcas/{$brand}/modelos/{$model}/anos/{$state}");
                                        if ($r->successful()) {
                                            $data = $r->json();
                                            $set('fipe_code', $data['CodigoFipe'] ?? null);
                                            $set('fuel_type', $data['Combustivel'] ?? null);
                                            $set('model_year', $data['AnoModelo'] ?? null);

                                            // Converte "R$ 15.000,00" → float
                                            $valor = (float) str_replace(
                                                ['R$', '.', ' ', ','],
                                                ['',   '',  '',  '.'],
                                                $data['Valor'] ?? '0'
                                            );
                                            $set('fipe_price', $valor);

                                            \Filament\Notifications\Notification::make()
                                                ->title("✅ FIPE importada: R$ " . number_format($valor, 0, ',', '.'))
                                                ->success()->send();
                                        }
                                    }
                                }),

                        ])->columns(3),

                    // ── Identificação ──────────────────────────────────────────
                    Section::make('🚗 Identificação do Veículo')
                        ->schema([
                            TextInput::make('brand')
                                ->label('Marca')
                                ->required()
                                ->maxLength(60),

                            TextInput::make('model')
                                ->label('Modelo')
                                ->required()
                                ->maxLength(80),

                            TextInput::make('version')
                                ->label('Versão')
                                ->required()
                                ->columnSpanFull()
                                ->maxLength(120)
                                ->placeholder('Ex: 2.0 XRE Hybrid CVT'),

                            TextInput::make('plate')
                                ->label('Placa')
                                ->required()
                                ->mask('aaa-9*99')
                                ->placeholder('ABC-1234')
                                ->extraInputAttributes(['style' => 'font-family: monospace'])
                                ->maxLength(8),

                            TextInput::make('manufacture_year')
                                ->label('Ano Fabricação')
                                ->required()
                                ->numeric()
                                ->minValue(1990)
                                ->maxValue(now()->year + 1),

                            TextInput::make('model_year')
                                ->label('Ano Modelo')
                                ->required()
                                ->numeric()
                                ->minValue(1990)
                                ->maxValue(now()->year + 2),

                            Select::make('category')
                                ->label('Carroceria')
                                ->options([
                                    'SUV'         => '🚙 SUV',
                                    'Sedan'       => '🚗 Sedan',
                                    'Hatch'       => '🚘 Hatch',
                                    'Pickup'      => '🛻 Pickup',
                                    'Minivan'     => '🚐 Minivan',
                                    'Conversível' => '🏎️ Conversível',
                                    'Outro'       => '🔹 Outro',
                                ])
                                ->searchable()
                                ->native(false),

                            TextInput::make('fipe_code')
                                ->label('Código FIPE')
                                ->extraInputAttributes(['style' => 'font-family: monospace'])
                                ->maxLength(10),
                        ])->columns(2),

                    // ── Especificações Técnicas ────────────────────────────────
                    Section::make('⚙️ Especificações Técnicas')
                        ->schema([
                            TextInput::make('mileage')
                                ->label('Quilometragem')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->suffix('km'),

                            Select::make('fuel_type')
                                ->label('Combustível')
                                ->options([
                                    'Flex'      => '⛽ Flex',
                                    'Gasolina'  => '🔴 Gasolina',
                                    'Diesel'    => '🟤 Diesel',
                                    'Elétrico'  => '⚡ Elétrico',
                                    'Híbrido'   => '🌿 Híbrido',
                                    'GNV'       => '🔵 GNV',
                                    'Etanol'    => '🌽 Etanol',
                                ])
                                ->native(false),

                            Select::make('transmission')
                                ->label('Câmbio')
                                ->options([
                                    'Manual'          => '🔧 Manual',
                                    'Automático'      => '🤖 Automático',
                                    'CVT'             => '〰️ CVT',
                                    'Automatizado'    => '🔄 Automatizado',
                                    'Automático (8AT)'=> '🤖 Automático (8AT)',
                                    'Automático (9AT)'=> '🤖 Automático (9AT)',
                                    'Automático (7DCT)'=> '🤖 Automático (7DCT)',
                                ])
                                ->native(false),

                            TextInput::make('engine')
                                ->label('Motor')
                                ->placeholder('Ex: 2.0 TwinPower Turbo')
                                ->maxLength(60),

                            TextInput::make('color')
                                ->label('Cor')
                                ->placeholder('Ex: Prata Metálico')
                                ->maxLength(40),

                            TextInput::make('doors')
                                ->label('Portas')
                                ->numeric()
                                ->default(4)
                                ->minValue(2)
                                ->maxValue(5),
                        ])->columns(3),

                    // ── Mídias ─────────────────────────────────────────────────
                    Section::make('📷 Fotos do Veículo')
                        ->schema([
                            FileUpload::make('media')
                                ->label('Galeria de Imagens')
                                ->multiple()
                                ->image()
                                ->reorderable()
                                ->appendFiles()
                                ->directory('vehicles')
                                ->imageEditor()
                                ->maxSize(5120)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->helperText('Máx. 5 MB por foto. A primeira imagem será a capa.')
                                ->columnSpanFull(),
                        ]),

                ])->columnSpan(2),

                // ─── Coluna Lateral (1/3) ────────────────────────────────────
                Group::make()->schema([

                    // ── Financeiro ────────────────────────────────────────────
                    Section::make('💰 Precificação')
                        ->schema([
                            TextInput::make('fipe_price')
                                ->label('Tabela FIPE')
                                ->numeric()
                                ->prefix('R$')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                    self::calcularMargem($get, $set);
                                }),

                            TextInput::make('sale_price')
                                ->label('Preço de Venda')
                                ->numeric()
                                ->required()
                                ->prefix('R$')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                    self::calcularMargem($get, $set);
                                }),

                            TextInput::make('profit_margin')
                                ->label('% abaixo da FIPE')
                                ->numeric()
                                ->suffix('%')
                                ->readOnly()
                                ->helperText('Calculado automaticamente'),
                        ]),

                    // ── Localização ───────────────────────────────────────────
                    Section::make('📍 Localização')
                        ->schema([
                            TextInput::make('location.name')
                                ->label('Nome do Pátio')
                                ->placeholder('Ex: Pátio São Paulo')
                                ->maxLength(80),

                            TextInput::make('location.city')
                                ->label('Cidade')
                                ->placeholder('Ex: São Paulo')
                                ->maxLength(60),

                            TextInput::make('location.state')
                                ->label('UF')
                                ->maxLength(2)
                                ->placeholder('SP'),
                        ]),

                    // ── Status & Destaques ────────────────────────────────────
                    Section::make('🏷️ Status & Destaques')
                        ->schema([
                            Select::make('status')
                                ->label('Disponibilidade')
                                ->options([
                                    'available' => '✅ Disponível',
                                    'reserved'  => '⏳ Reservado',
                                    'sold'      => '🔴 Vendido',
                                ])
                                ->default('available')
                                ->required()
                                ->native(false),

                            Toggle::make('is_on_sale')
                                ->label('🏷️ Em Oferta')
                                ->helperText('Destaque especial de preço')
                                ->default(false),

                            Toggle::make('is_just_arrived')
                                ->label('🆕 Recém Chegado')
                                ->helperText('Badge "Novo no estoque"')
                                ->default(false),

                            Toggle::make('has_report')
                                ->label('📋 Possui Laudo Cautelar')
                                ->default(false),

                            Toggle::make('has_factory_warranty')
                                ->label('🛡️ Garantia de Fábrica')
                                ->default(false),
                        ]),

                    // ── Acessórios ────────────────────────────────────────────
                    Section::make('✨ Acessórios')
                        ->schema([
                            TagsInput::make('accessories')
                                ->label('Lista de Acessórios')
                                ->placeholder('Digite e pressione Enter')
                                ->helperText('Ex: Teto Solar, Câmera 360°, Bancos de Couro')
                                ->default([]),
                        ]),

                ])->columnSpan(1),

            ])->columns(3);
    }

    /** Calcula % desconto FIPE e atualiza profit_margin */
    private static function calcularMargem(Get $get, Set $set): void
    {
        $fipe  = (float) ($get('fipe_price') ?? 0);
        $venda = (float) ($get('sale_price') ?? 0);

        if ($fipe > 0 && $venda > 0) {
            $margem = round((1 - $venda / $fipe) * 100, 1);
            $set('profit_margin', $margem);
        }
    }
}
