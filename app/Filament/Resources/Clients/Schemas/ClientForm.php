<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Dados da Empresa (PJ)')
                ->schema([
                    TextInput::make('razao_social')
                        ->label('Razão Social')
                        ->required(),

                    TextInput::make('nome_fantasia')
                        ->label('Nome Fantasia'),

                    TextInput::make('cnpj')
                        ->label('CNPJ')
                        ->mask('99.999.999/9999-99')
                        ->unique(ignoreRecord: true),

                    TextInput::make('inscricao_estadual')
                        ->label('Inscrição Estadual'),

                    TextInput::make('name')
                        ->label('Responsável (Nome Completo)')
                        ->required(),

                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),

                    TextInput::make('phone')
                        ->label('WhatsApp / Telefone')
                        ->mask('(99) 9 9999-9999'),

                    TextInput::make('cpf')
                        ->label('CPF do Responsável')
                        ->mask('999.999.999-99'),
                ])->columns(2),

            Section::make('Endereço')
                ->schema([
                    TextInput::make('cep')
                        ->label('CEP')
                        ->mask('99999-999')
                        ->suffixAction(
                            \Filament\Actions\Action::make('buscar_cep')
                                ->label('Buscar')
                                ->icon('heroicon-o-magnifying-glass')
                        ),

                    TextInput::make('logradouro')->label('Rua / Avenida'),
                    TextInput::make('numero')->label('Número'),
                    TextInput::make('complemento')->label('Complemento'),
                    TextInput::make('bairro')->label('Bairro'),
                    TextInput::make('cidade')->label('Cidade'),

                    Select::make('estado')
                        ->label('Estado (UF)')
                        ->options([
                            'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
                            'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal',
                            'ES' => 'Espírito Santo', 'GO' => 'Goiás', 'MA' => 'Maranhão',
                            'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul', 'MG' => 'Minas Gerais',
                            'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná', 'PE' => 'Pernambuco',
                            'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
                            'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima',
                            'SC' => 'Santa Catarina', 'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins',
                        ]),
                ])->columns(2),

            Section::make('Status e Controle de Acesso')
                ->schema([
                    Select::make('status')
                        ->label('Status da Conta')
                        ->options([
                            'pendente'  => '⏳ Aguardando Aprovação',
                            'ativo'     => '✅ Ativo',
                            'bloqueado' => '🚫 Bloqueado',
                        ])
                        ->default('pendente')
                        ->required(),

                    DateTimePicker::make('aprovado_em')
                        ->label('Aprovado em')
                        ->displayFormat('d/m/Y H:i')
                        ->disabled(),

                    Textarea::make('observacoes')
                        ->label('Observações Internas')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }
}
