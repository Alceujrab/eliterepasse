<?php

namespace App\Filament\Resources\LandingSettings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class LandingSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('landing_tabs')
                    ->columnSpanFull()
                    ->tabs([

                        // ── Topo / Cabeçalho ───────────────────────
                        Tab::make('Topo / Menu')
                            ->icon('heroicon-o-bars-3')
                            ->schema([
                                Section::make('Logomarca')
                                    ->schema([
                                        FileUpload::make('logo_path')
                                            ->label('Logo do Site')
                                            ->image()
                                            ->directory('landing')
                                            ->disk('public')
                                            ->imageEditor()
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                                            ->helperText('Recomendado: fundo transparente, PNG ou SVG. Máx. 2 MB.'),
                                    ]),

                                Section::make('Itens do Menu')
                                    ->description('Links que aparecem no menu de navegação do topo do site.')
                                    ->schema([
                                        Repeater::make('menu_items')
                                            ->label('')
                                            ->schema([
                                                TextInput::make('label')
                                                    ->label('Texto')
                                                    ->required()
                                                    ->placeholder('Ex: Modelos'),
                                                TextInput::make('url')
                                                    ->label('Link')
                                                    ->required()
                                                    ->placeholder('Ex: #modelos ou /pagina'),
                                            ])
                                            ->columns(2)
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ── Hero / Banner ───────────────────────────
                        Tab::make('Hero / Banner')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Textos do Hero')
                                    ->description('Título e subtítulo que aparecem sobre o banner principal.')
                                    ->schema([
                                        TextInput::make('hero_title')
                                            ->label('Título Principal')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('hero_subtitle')
                                            ->label('Subtítulo')
                                            ->required()
                                            ->maxLength(500),
                                    ]),

                                Section::make('Banners (Carrossel)')
                                    ->description('Os banners são gerenciados em uma tela própria. Acesse "Banners do Site" no menu Configurações.')
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('banner_info')
                                            ->label('')
                                            ->content('→ Acesse o menu Configurações > Banners do Site para adicionar, editar e reordenar as imagens do carrossel.'),
                                    ]),
                            ]),

                        // ── Vantagens / Features ────────────────────
                        Tab::make('Vantagens')
                            ->icon('heroicon-o-star')
                            ->schema([
                                Section::make('Destaques do portal')
                                    ->description('Cards de vantagens exibidos na landing page.')
                                    ->schema([
                                        Repeater::make('features')
                                            ->label('')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Título')
                                                    ->required(),
                                                Textarea::make('description')
                                                    ->label('Descrição')
                                                    ->required()
                                                    ->rows(2),
                                                TextInput::make('icon')
                                                    ->label('Ícone')
                                                    ->placeholder('Nome Heroicon, ex: check-circle'),
                                            ])
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ── Sobre Nós ───────────────────────────────
                        Tab::make('Sobre Nós')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Section::make('Seção Sobre a Empresa')
                                    ->schema([
                                        TextInput::make('about_title')
                                            ->label('Título')
                                            ->maxLength(255)
                                            ->placeholder('Sobre a Elite Repasse'),
                                        Textarea::make('about_text')
                                            ->label('Texto')
                                            ->rows(5)
                                            ->placeholder('Conte um pouco sobre a empresa, missão e valores...'),
                                        FileUpload::make('about_image')
                                            ->label('Imagem da Seção')
                                            ->image()
                                            ->directory('landing')
                                            ->disk('public')
                                            ->imageEditor()
                                            ->maxSize(3072)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->helperText('Foto da loja, equipe ou fachada. Máx. 3 MB.'),
                                    ]),
                            ]),

                        // ── Contato / Mapa ──────────────────────────
                        Tab::make('Contato / Mapa')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make('Informações de Contato')
                                    ->schema([
                                        TextInput::make('contact_phone')
                                            ->label('Telefone')
                                            ->placeholder('(31) 99999-9999'),
                                        TextInput::make('contact_email')
                                            ->label('E-mail')
                                            ->email()
                                            ->placeholder('contato@eliterepasse.com.br'),
                                        TextInput::make('whatsapp_number')
                                            ->label('WhatsApp (com DDI+DDD)')
                                            ->required()
                                            ->placeholder('5531999999999'),
                                    ])->columns(3),

                                Section::make('Endereço e Localização')
                                    ->description('O mapa será exibido usando a chave do Google Maps cadastrada em Configurações Gerais.')
                                    ->schema([
                                        TextInput::make('contact_address')
                                            ->label('Endereço')
                                            ->placeholder('Rua Exemplo, 123')
                                            ->columnSpanFull(),
                                        TextInput::make('contact_city')
                                            ->label('Cidade')
                                            ->placeholder('Belo Horizonte'),
                                        TextInput::make('contact_state')
                                            ->label('Estado')
                                            ->placeholder('MG')
                                            ->maxLength(2),
                                        TextInput::make('contact_lat')
                                            ->label('Latitude')
                                            ->placeholder('-19.9191')
                                            ->helperText('Use Google Maps para obter'),
                                        TextInput::make('contact_lng')
                                            ->label('Longitude')
                                            ->placeholder('-43.9386')
                                            ->helperText('Use Google Maps para obter'),
                                    ])->columns(2),
                            ]),

                        // ── FAQ ─────────────────────────────────────
                        Tab::make('FAQ')
                            ->icon('heroicon-o-question-mark-circle')
                            ->schema([
                                Section::make('Perguntas Frequentes')
                                    ->schema([
                                        Repeater::make('faq')
                                            ->label('')
                                            ->schema([
                                                TextInput::make('question')
                                                    ->label('Pergunta')
                                                    ->required(),
                                                Textarea::make('answer')
                                                    ->label('Resposta')
                                                    ->required()
                                                    ->rows(3),
                                            ])
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ── Rodapé / Footer ─────────────────────────
                        Tab::make('Rodapé')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Conteúdo do Rodapé')
                                    ->schema([
                                        Textarea::make('footer_text')
                                            ->label('Texto do Rodapé')
                                            ->rows(2)
                                            ->placeholder('Descrição curta da empresa no rodapé'),
                                        Repeater::make('footer_links')
                                            ->label('Links do Rodapé')
                                            ->schema([
                                                TextInput::make('label')
                                                    ->label('Texto')
                                                    ->required(),
                                                TextInput::make('url')
                                                    ->label('Link')
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Redes Sociais')
                                    ->schema([
                                        TextInput::make('social_instagram')
                                            ->label('Instagram')
                                            ->url()
                                            ->placeholder('https://instagram.com/eliterepasse'),
                                        TextInput::make('social_facebook')
                                            ->label('Facebook')
                                            ->url()
                                            ->placeholder('https://facebook.com/eliterepasse'),
                                        TextInput::make('social_youtube')
                                            ->label('YouTube')
                                            ->url()
                                            ->placeholder('https://youtube.com/@eliterepasse'),
                                    ])->columns(3),
                            ]),
                    ]),
            ]);
    }
}
