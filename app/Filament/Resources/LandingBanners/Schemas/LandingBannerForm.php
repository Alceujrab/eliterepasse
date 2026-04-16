<?php

namespace App\Filament\Resources\LandingBanners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LandingBannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Título do Banner')
                    ->maxLength(255),

                TextInput::make('subtitle')
                    ->label('Subtítulo')
                    ->maxLength(255),

                FileUpload::make('image_path')
                    ->label('Imagem do Banner')
                    ->image()
                    ->required()
                    ->directory('banners')
                    ->disk('public')
                    ->imageEditor()
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('Resolução recomendada: 1920x600. Máx. 5 MB.')
                    ->columnSpanFull(),

                TextInput::make('button_text')
                    ->label('Texto do Botão')
                    ->placeholder('Ex: Cadastre-se agora')
                    ->maxLength(100),

                TextInput::make('button_url')
                    ->label('Link do Botão')
                    ->placeholder('/register')
                    ->maxLength(500),

                TextInput::make('order')
                    ->label('Ordem de Exibição')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Toggle::make('is_active')
                    ->label('Ativo')
                    ->default(true),
            ]);
    }
}
