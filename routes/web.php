<?php

use App\Http\Controllers\ContractSignatureController;
use App\Http\Controllers\Admin\OrderActionController;
use App\Http\Controllers\Admin\ContractActionController;
use App\Http\Controllers\Admin\ContractsIndexController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ModulePageController;
use App\Http\Controllers\Admin\OrdersIndexController;
use App\Http\Controllers\EvolutionWebhookController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureUserIsApproved;
use Illuminate\Support\Facades\Route;

// ─── Webhook Evolution GO (sem CSRF, sem auth) ────────────────────────
Route::post('/webhook/evolution', EvolutionWebhookController::class)
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('webhook.evolution');

Route::get('/', \App\Livewire\LandingPage::class)->name('home');

// ─── Rotas Públicas de Contrato (acesso via token único) ─────────────
Route::get('/contrato/assinar/{token}',  [ContractSignatureController::class, 'show'])->name('contrato.assinar.show');
Route::post('/contrato/assinar/{token}', [ContractSignatureController::class, 'store'])->name('contrato.assinar.store');
Route::get('/contrato/assinado',         fn () => view('contratos.assinado'))->name('contrato.assinado');

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
    Route::get('meus-documentos/{id}/download', [\App\Http\Controllers\DocumentDownloadController::class, 'downloadContract'])->name('contrato.download');

    Route::get('documentos-elite', \App\Livewire\DocumentosElite::class)->name('documentos-elite');

    Route::get('favoritos', \App\Livewire\Favoritos::class)->name('favoritos');

    Route::get('notificacoes', \App\Livewire\MinhasNotificacoes::class)->name('notificacoes');

    Route::get('meus-pedidos', \App\Livewire\MeusPedidos::class)->name('meus-pedidos');

    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';

// Novo Admin v2 (sem Filament) - executa em paralelo ao admin legado.
Route::middleware(['auth', 'verified', EnsureAdmin::class])->prefix('painel-admin')->name('admin.v2.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');

    Route::get('/pedidos', OrdersIndexController::class)->name('orders.index');
    Route::post('/pedidos/{order}/confirmar', [OrderActionController::class, 'confirm'])->name('orders.confirm');
    Route::post('/pedidos/{order}/gerar-contrato', [OrderActionController::class, 'generateContract'])->name('orders.generate-contract');
    Route::post('/pedidos/{order}/gerar-fatura', [OrderActionController::class, 'generateInvoice'])->name('orders.generate-invoice');
    Route::post('/pedidos/{order}/confirmar-pagamento', [OrderActionController::class, 'confirmPayment'])->name('orders.confirm-payment');
    Route::post('/pedidos/{order}/cancelar', [OrderActionController::class, 'cancel'])->name('orders.cancel');

    Route::get('/contratos', ContractsIndexController::class)->name('contracts.index');
    Route::post('/contratos/{contract}/enviar-assinatura', [ContractActionController::class, 'sendToSign'])->name('contracts.send-to-sign');
    Route::post('/contratos/{contract}/copiar-link', [ContractActionController::class, 'copyLink'])->name('contracts.copy-link');

    Route::get('/modulo/{module}', ModulePageController::class)
        ->where('module', '[a-z\-]+')
        ->name('module');
});
