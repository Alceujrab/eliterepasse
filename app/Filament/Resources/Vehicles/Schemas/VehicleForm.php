<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Group::make()->schema([
                    \Filament\Forms\Components\Section::make('Integração FIPE')
                        ->description('Selecione os dados via FIPE para preencher a ficha automaticamente.')
                        ->schema([
                            \Filament\Forms\Components\Select::make('fipe_brand_id')
                                ->label('Busca por Marca')
                                ->options(function () {
                                    return cache()->remember('fipe.brands', 86400, function () {
                                        $response = \Illuminate\Support\Facades\Http::get('https://parallelum.com.br/fipe/api/v1/carros/marcas');
                                        if ($response->successful()) {
                                            return collect($response->json())->pluck('nome', 'codigo')->toArray();
                                        }
                                        return [];
                                    });
                                })
                                ->searchable()
                                ->live()
                                ->dehydrated(false)
                                ->afterStateUpdated(function ($state, \Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                                    $set('fipe_model_id', null);
                                    $set('fipe_year_id', null);
                                    
                                    // Set the real brand name
                                    $brands = cache('fipe.brands', []);
                                    if(isset($brands[$state])) {
                                        $set('brand', $brands[$state]);
                                    }
                                }),

                            \Filament\Forms\Components\Select::make('fipe_model_id')
                                ->label('Busca por Modelo')
                                ->options(function (\Filament\Forms\Get $get) {
                                    $brand = $get('fipe_brand_id');
                                    if (! $brand) return [];
                                    
                                    return cache()->remember("fipe.models.{$brand}", 86400, function () use ($brand) {
                                        $response = \Illuminate\Support\Facades\Http::get("https://parallelum.com.br/fipe/api/v1/carros/marcas/{$brand}/modelos");
                                        if ($response->successful()) {
                                            return collect($response->json()['modelos'])->pluck('nome', 'codigo')->toArray();
                                        }
                                        return [];
                                    });
                                })
                                ->searchable()
                                ->live()
                                ->dehydrated(false)
                                ->disabled(fn (\Filament\Forms\Get $get) => empty($get('fipe_brand_id')))
                                ->afterStateUpdated(function ($state, \Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                                    $set('fipe_year_id', null);
                                    $brand = $get('fipe_brand_id');
                                    $models = cache("fipe.models.{$brand}", []);
                                    if(isset($models[$state])) {
                                        $set('model', $models[$state]);
                                    }
                                }),

                            \Filament\Forms\Components\Select::make('fipe_year_id')
                                ->label('Ano / Emissão FIPE')
                                ->options(function (\Filament\Forms\Get $get) {
                                    $brand = $get('fipe_brand_id');
                                    $model = $get('fipe_model_id');
                                    if (! $brand || ! $model) return [];

                                    $response = \Illuminate\Support\Facades\Http::get("https://parallelum.com.br/fipe/api/v1/carros/marcas/{$brand}/modelos/{$model}/anos");
                                    if ($response->successful()) {
                                        return collect($response->json())->pluck('nome', 'codigo')->toArray();
                                    }
                                    return [];
                                })
                                ->searchable()
                                ->live()
                                ->dehydrated(false)
                                ->disabled(fn (\Filament\Forms\Get $get) => empty($get('fipe_model_id')))
                                ->afterStateUpdated(function ($state, \Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                                    $brand = $get('fipe_brand_id');
                                    $model = $get('fipe_model_id');
                                    if ($brand && $model && $state) {
                                        $response = \Illuminate\Support\Facades\Http::get("https://parallelum.com.br/fipe/api/v1/carros/marcas/{$brand}/modelos/{$model}/anos/{$state}");
                                        if ($response->successful()) {
                                            $data = $response->json();
                                            // Atualiza os campos reais com os dados da FIPE
                                            $set('fipe_code', $data['CodigoFipe']);
                                            $set('fuel_type', $data['Combustivel']);
                                            $set('model_year', $data['AnoModelo']);
                                            
                                            // Converte "R$ 15.000,00" para float
                                            $valorStr = str_replace(['R$', '.', ' ', ','], ['', '', '', '.'], $data['Valor']);
                                            $set('fipe_price', (float) $valorStr);
                                            
                                            \Filament\Notifications\Notification::make()
                                                ->title('Dados FIPE importados com sucesso!')
                                                ->success()
                                                ->send();
                                        }
                                    }
                                }),

                        ])->columns(3),

                    \Filament\Forms\Components\Section::make('Identificação do Veículo')
                        ->schema([
                            TextInput::make('brand')
                                ->label('Marca')
                                ->required(),
                            TextInput::make('model')
                                ->label('Modelo')
                                ->required(),
                            TextInput::make('version')
                                ->label('Versão do Carro')
                                ->required()
                                ->columnSpanFull(),
                            TextInput::make('plate')
                                ->label('Placa')
                                ->required(),
                            TextInput::make('manufacture_year')
                                ->label('Ano Fabricação')
                                ->required()
                                ->numeric(),
                            TextInput::make('model_year')
                                ->label('Ano Modelo')
                                ->required()
                                ->numeric(),
                            TextInput::make('category')
                                ->label('Carroceria')
                                ->default(null),
                            TextInput::make('fipe_code')
                                ->label('Código FIPE')
                                ->default(null),
                        ])->columns(2),
                        
                    \Filament\Forms\Components\Section::make('Especificações Técnicas')
                        ->schema([
                            TextInput::make('mileage')
                                ->label('Quilometragem')
                                ->required()
                                ->numeric()
                                ->default(0),
                            TextInput::make('fuel_type')
                                ->label('Combustível')
                                ->default(null),
                            TextInput::make('transmission')
                                ->label('Câmbio')
                                ->default(null),
                            TextInput::make('engine')
                                ->label('Motor')
                                ->default(null),
                            TextInput::make('color')
                                ->label('Cor')
                                ->default(null),
                            TextInput::make('doors')
                                ->label('Portas')
                                ->numeric()
                                ->default(null),
                        ])->columns(3),
                        
                    \Filament\Forms\Components\Section::make('Mídias e Fotos')
                        ->schema([
                            \Filament\Forms\Components\FileUpload::make('media')
                                ->label('Fotos do Veículo')
                                ->multiple()
                                ->image()
                                ->reorderable()
                                ->appendFiles()
                                ->directory('vehicles')
                                ->columnSpanFull(),
                        ])
                ])->columnSpan(2),

                \Filament\Forms\Components\Group::make()->schema([
                    \Filament\Forms\Components\Section::make('Financeiro')
                        ->schema([
                            TextInput::make('fipe_price')
                                ->label('Preço FIPE')
                                ->numeric()
                                ->default(null)
                                ->prefix('R$'),
                            TextInput::make('sale_price')
                                ->label('Preço de Venda')
                                ->numeric()
                                ->default(null)
                                ->prefix('R$'),
                            TextInput::make('profit_margin')
                                ->label('Margem Lucro')
                                ->numeric()
                                ->default(null)
                                ->prefix('R$'),
                        ]),
                        
                    \Filament\Forms\Components\Section::make('Status & Destaques')
                        ->schema([
                            Select::make('status')
                                ->label('Disponibilidade')
                                ->options([
                                    'available' => 'Disponível',
                                    'reserved' => 'Reservado',
                                    'sold' => 'Vendido'
                                ])
                                ->default('available')
                                ->required(),
                                
                            Toggle::make('has_report')
                                ->label('Carro com Laudo Cautelar')
                                ->default(false),
                            Toggle::make('has_factory_warranty')
                                ->label('Garantia de Fábrica')
                                ->default(false),
                            Toggle::make('is_on_sale')
                                ->label('Veículo em Oferta')
                                ->default(false),
                            Toggle::make('is_just_arrived')
                                ->label('Acabou de chegar')
                                ->default(false),
                        ]),
                        
                    \Filament\Forms\Components\Section::make('Acessórios')
                        ->schema([
                            // Por enquanto JSON raw (Pode ser refatorado pra TagsInput futuramente)
                            \Filament\Forms\Components\TagsInput::make('accessories')
                                ->label('Acessórios (Pressione Enter)')
                                ->default([]),
                        ])
                ])->columnSpan(1),
            ])->columns(3);
    }
}
