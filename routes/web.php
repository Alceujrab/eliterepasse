<?php

use App\Http\Middleware\EnsureUserIsApproved;
use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\LandingPage::class)->name('home');

// Página de aguardando aprovação (pública para usuários logados)
Route::get('aguardando-aprovacao', function () {
    return view('auth.aguardando-aprovacao');
})->middleware('auth')->name('aguardando.aprovacao');

// Todas as rotas protegidas passam pelo middleware de aprovação
Route::middleware(['auth', 'verified', EnsureUserIsApproved::class])->group(function () {

    Route::get('dashboard', \App\Livewire\Vitrine::class)->name('dashboard');

    Route::get('veiculo/{id}', \App\Livewire\VehicleDetails::class)->name('vehicle.details');

    Route::get('pedidos', \App\Livewire\MeusPedidos::class)->name('pedidos');

    Route::get('financeiro', \App\Livewire\Financeiro::class)->name('financeiro');

    Route::get('suporte', \App\Livewire\Suporte::class)->name('suporte');

    Route::get('meus-documentos', \App\Livewire\MeusDocumentos::class)->name('meus-documentos');

    Route::get('documentos-elite', \App\Livewire\DocumentosElite::class)->name('documentos-elite');

    Route::get('favoritos', \App\Livewire\Favoritos::class)->name('favoritos');

    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
