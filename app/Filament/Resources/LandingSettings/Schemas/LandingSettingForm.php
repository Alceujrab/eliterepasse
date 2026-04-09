<?php

namespace App\Filament\Resources\LandingSettings\Schemas;

use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Schema;

use Filament\Schemas\Components\Repeater;

class LandingSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('hero_title')
                    ->required(),
                TextInput::make('hero_subtitle')
                    ->required(),
                TextInput::make('whatsapp_number')
                    ->required(),
                Repeater::make('features')
                    ->schema([
                        TextInput::make('title')->required(),
                        Textarea::make('description')->required(),
                        TextInput::make('icon')->placeholder('Heroicon name, e.g. check-circle'),
                    ])
                    ->columnSpanFull(),
                Repeater::make('faq')
                    ->schema([
                        TextInput::make('question')->required(),
                        Textarea::make('answer')->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
