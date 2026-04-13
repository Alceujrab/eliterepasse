<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Models\Document;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Documento')
                    ->description('Classifique e atribua o documento ao cliente ou veículo correto.')
                    ->columns(2)
                    ->schema([
                        Select::make('tipo')
                            ->label('Tipo de Documento')
                            ->options(Document::tipoLabels())
                            ->required()
                            ->searchable(),

                        TextInput::make('titulo')
                            ->label('Título do Arquivo')
                            ->placeholder('Ex: ATPV-e Assinado')
                            ->required()
                            ->maxLength(255),

                        Select::make('user_id')
                            ->label('Cliente / Usuário')
                            ->options(User::where('is_admin', false)->pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->helperText('Selecione o proprietário deste documento.'),

                        Select::make('vehicle_id')
                            ->label('Veículo Associado')
                            ->options(fn () => Vehicle::all()->mapWithKeys(fn($v) => [$v->id => "{$v->brand} {$v->model} {$v->model_year} — {$v->plate}"]))
                            ->searchable()
                            ->nullable(),

                        DatePicker::make('validade')
                            ->label('Data de Validade')
                            ->nullable(),

                        Toggle::make('visivel_cliente')
                            ->label('Visível para o Cliente?')
                            ->default(true)
                            ->onIcon('heroicon-s-eye')
                            ->offIcon('heroicon-s-eye-slash')
                            ->helperText('Habilita o download no portal "Meus Documentos".'),
                        
                        Select::make('status')
                            ->label('Status')
                            ->options(Document::statusLabels())
                            ->default('verificado')
                            ->required(),
                    ]),

                Section::make('Arquivo Físico')
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('Anexar Arquivo')
                            ->directory('documentos')
                            ->preserveFilenames()
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120) // 5MB
                            ->required()
                            ->helperText('Tamanho máximo 5MB. Formatos: PDF, JPG, PNG.')
                            ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Set $set) {
                                if ($state) {
                                    if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                        $set('nome_original', $state->getClientOriginalName());
                                        $set('tamanho', $state->getSize());
                                        $set('mime_type', $state->getMimeType());
                                    } elseif (is_string($state)) {
                                        $set('nome_original', basename($state));
                                        $set('tamanho', \Illuminate\Support\Facades\Storage::disk('public')->size($state));
                                        $set('mime_type', \Illuminate\Support\Facades\Storage::disk('public')->mimeType($state));
                                    }
                                }
                            }),

                        TextInput::make('nome_original')
                            ->label('Nome do Arquivo Físico')
                            ->disabled()
                            ->dehydrated(true),
                            
                        TextInput::make('tamanho')
                            ->label('Bytes')
                            ->hidden()
                            ->dehydrated(true),
                            
                        TextInput::make('mime_type')
                            ->label('MimeType')
                            ->hidden()
                            ->dehydrated(true),
                    ]),
            ]);
    }
}
