<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\LandingPage::class)->name('home');

Route::get('dashboard', \App\Livewire\Vitrine::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('pedidos', \App\Livewire\MeusPedidos::class)
    ->middleware(['auth', 'verified'])
    ->name('pedidos');

Route::get('financeiro', \App\Livewire\Financeiro::class)
    ->middleware(['auth', 'verified'])
    ->name('financeiro');

Route::get('suporte', \App\Livewire\Suporte::class)
    ->middleware(['auth', 'verified'])
    ->name('suporte');

Route::get('meus-documentos', \App\Livewire\MeusDocumentos::class)
    ->middleware(['auth', 'verified'])
    ->name('meus-documentos');

Route::get('documentos-elite', \App\Livewire\DocumentosElite::class)
    ->middleware(['auth', 'verified'])
    ->name('documentos-elite');

Route::get('favoritos', \App\Livewire\Favoritos::class)
    ->middleware(['auth', 'verified'])
    ->name('favoritos');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
