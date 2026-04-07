<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('pedidos', \App\Livewire\MeusPedidos::class)
    ->middleware(['auth', 'verified'])
    ->name('pedidos');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
