<?php

use App\Http\Controllers\ContractSignatureController;
use App\Http\Controllers\Admin\OrderActionController;
use App\Http\Controllers\Admin\ContractActionController;
use App\Http\Controllers\Admin\ContractsIndexController;
use App\Http\Controllers\Admin\ContractShowController;
use App\Http\Controllers\Admin\ClientActionController;
use App\Http\Controllers\Admin\ClientsIndexController;
use App\Http\Controllers\Admin\ClientShowController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmailTemplateActionController;
use App\Http\Controllers\Admin\EmailTemplateCreateController;
use App\Http\Controllers\Admin\EmailTemplateShowController;
use App\Http\Controllers\Admin\EmailTemplatesIndexController;
use App\Http\Controllers\Admin\EvolutionInstanceActionController;
use App\Http\Controllers\Admin\EvolutionInstanceCreateController;
use App\Http\Controllers\Admin\EvolutionInstanceShowController;
use App\Http\Controllers\Admin\EvolutionInstancesIndexController;
use App\Http\Controllers\Admin\WhatsappInboxActionController;
use App\Http\Controllers\Admin\WhatsappInboxIndexController;
use App\Http\Controllers\Admin\FinancialShowController;
use App\Http\Controllers\Admin\FinanceiroIndexController;
use App\Http\Controllers\Admin\LandingBannerActionController;
use App\Http\Controllers\Admin\LandingBannersIndexController;
use App\Http\Controllers\Admin\LandingSettingsActionController;
use App\Http\Controllers\Admin\LandingSettingsIndexController;
use App\Http\Controllers\Admin\AboutPageSettingsActionController;
use App\Http\Controllers\Admin\AboutPageSettingsIndexController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SystemSettingsActionController;
use App\Http\Controllers\Admin\SystemSettingsIndexController;
use App\Http\Controllers\Admin\DocumentActionController;
use App\Http\Controllers\Admin\DocumentsIndexController;
use App\Http\Controllers\Admin\OrdersIndexController;
use App\Http\Controllers\Admin\OrderShowController;
use App\Http\Controllers\Admin\TicketActionController;
use App\Http\Controllers\Admin\TicketsIndexController;
use App\Http\Controllers\Admin\VehicleActionController;
use App\Http\Controllers\Admin\VehiclesIndexController;
use App\Http\Controllers\Admin\VehicleShowController;
use App\Http\Controllers\EvolutionWebhookController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureUserIsApproved;
use Illuminate\Support\Facades\Route;

// ─── Webhook Evolution GO (sem CSRF, sem auth) ────────────────────────
Route::post('/webhook/evolution', EvolutionWebhookController::class)
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('webhook.evolution');

Route::get('/', \App\Livewire\LandingPage::class)->name('home');
Route::get('/contato', \App\Livewire\Contato::class)->name('contato');
Route::get('/sobre-nos', \App\Livewire\SobreNos::class)->name('sobre-nos');

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

    Route::get('/clientes', ClientsIndexController::class)->name('clients.index');
    Route::get('/clientes/{client}', ClientShowController::class)->name('clients.show');
    Route::post('/clientes/{client}/aprovar', [ClientActionController::class, 'approve'])->name('clients.approve');
    Route::post('/clientes/{client}/bloquear', [ClientActionController::class, 'block'])->name('clients.block');

    Route::get('/veiculos', VehiclesIndexController::class)->name('vehicles.index');
    Route::get('/veiculos/{vehicle}', VehicleShowController::class)->name('vehicles.show');
    Route::post('/veiculos/{vehicle}/status', [VehicleActionController::class, 'updateStatus'])->name('vehicles.status');

    Route::get('/pedidos', OrdersIndexController::class)->name('orders.index');
    Route::get('/pedidos/{order}', OrderShowController::class)->name('orders.show');
    Route::post('/pedidos/{order}/confirmar', [OrderActionController::class, 'confirm'])->name('orders.confirm');
    Route::post('/pedidos/{order}/gerar-contrato', [OrderActionController::class, 'generateContract'])->name('orders.generate-contract');
    Route::post('/pedidos/{order}/gerar-fatura', [OrderActionController::class, 'generateInvoice'])->name('orders.generate-invoice');
    Route::post('/pedidos/{order}/confirmar-pagamento', [OrderActionController::class, 'confirmPayment'])->name('orders.confirm-payment');
    Route::post('/pedidos/{order}/cancelar', [OrderActionController::class, 'cancel'])->name('orders.cancel');
    Route::post('/pedidos/{order}/documentos/disponibilizar', [OrderActionController::class, 'publishDocument'])->name('orders.publish-document');
    Route::post('/pedidos/{order}/documentos/despachar', [OrderActionController::class, 'registerDispatch'])->name('orders.register-dispatch');
    Route::post('/pedidos/{order}/documentos/reenviar-notificacao', [OrderActionController::class, 'resendShipmentNotification'])->name('orders.resend-shipment-notification');
    Route::post('/pedidos/{order}/documentos/marcar-entregue', [OrderActionController::class, 'markShipmentDelivered'])->name('orders.mark-shipment-delivered');

    Route::get('/contratos', ContractsIndexController::class)->name('contracts.index');
    Route::get('/contratos/{contract}', ContractShowController::class)->name('contracts.show');
    Route::post('/contratos/{contract}/enviar-assinatura', [ContractActionController::class, 'sendToSign'])->name('contracts.send-to-sign');
    Route::post('/contratos/{contract}/copiar-link', [ContractActionController::class, 'copyLink'])->name('contracts.copy-link');

    Route::get('/documentos', DocumentsIndexController::class)->name('documents.index');
    Route::post('/documentos', [DocumentActionController::class, 'store'])->name('documents.store');
    Route::post('/documentos/{document}/verificar', [DocumentActionController::class, 'verify'])->name('documents.verify');
    Route::post('/documentos/{document}/rejeitar', [DocumentActionController::class, 'reject'])->name('documents.reject');

    Route::get('/tickets', TicketsIndexController::class)->name('tickets.index');
    Route::post('/tickets', [TicketActionController::class, 'store'])->name('tickets.store');
    Route::post('/tickets/{ticket}/reply', [TicketActionController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/assign', [TicketActionController::class, 'assign'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/status', [TicketActionController::class, 'updateStatus'])->name('tickets.status');

    Route::get('/financeiro', FinanceiroIndexController::class)->name('financeiro.index');
    Route::get('/financeiro/{financial}', FinancialShowController::class)->name('financeiro.show');

    Route::get('/relatorios', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/relatorios/exportar.csv', [ReportsController::class, 'exportCsv'])->name('reports.export-csv');
    Route::get('/relatorios/exportar.pdf', [ReportsController::class, 'exportPdf'])->name('reports.export-pdf');

    Route::get('/email-templates', EmailTemplatesIndexController::class)->name('email-templates.index');
    Route::get('/email-templates/criar', EmailTemplateCreateController::class)->name('email-templates.create');
    Route::post('/email-templates', [EmailTemplateActionController::class, 'store'])->name('email-templates.store');
    Route::get('/email-templates/{emailTemplate}', EmailTemplateShowController::class)->name('email-templates.show');
    Route::post('/email-templates/{emailTemplate}', [EmailTemplateActionController::class, 'update'])->name('email-templates.update');
    Route::post('/email-templates/{emailTemplate}/gerar-ia', [EmailTemplateActionController::class, 'generateAi'])->name('email-templates.generate-ai');

    Route::get('/whatsapp-instancias', EvolutionInstancesIndexController::class)->name('whatsapp-instancias.index');
    Route::get('/whatsapp-instancias/criar', EvolutionInstanceCreateController::class)->name('whatsapp-instancias.create');
    Route::post('/whatsapp-instancias', [EvolutionInstanceActionController::class, 'store'])->name('whatsapp-instancias.store');
    Route::get('/whatsapp-instancias/{evolutionInstance}', EvolutionInstanceShowController::class)->name('whatsapp-instancias.show');
    Route::post('/whatsapp-instancias/{evolutionInstance}', [EvolutionInstanceActionController::class, 'update'])->name('whatsapp-instancias.update');
    Route::post('/whatsapp-instancias/{evolutionInstance}/testar', [EvolutionInstanceActionController::class, 'testConnection'])->name('whatsapp-instancias.test-connection');
    Route::post('/whatsapp-instancias/{evolutionInstance}/logout', [EvolutionInstanceActionController::class, 'logout'])->name('whatsapp-instancias.logout');
    Route::post('/whatsapp-instancias/{evolutionInstance}/teste-envio', [EvolutionInstanceActionController::class, 'sendTest'])->name('whatsapp-instancias.send-test');

    Route::get('/whatsapp-inbox', WhatsappInboxIndexController::class)->name('whatsapp-inbox.index');
    Route::post('/whatsapp-inbox/{ticket}/responder', [WhatsappInboxActionController::class, 'reply'])->name('whatsapp-inbox.reply');
    Route::post('/whatsapp-inbox/{ticket}/status', [WhatsappInboxActionController::class, 'updateStatus'])->name('whatsapp-inbox.update-status');

    Route::get('/landing-settings', LandingSettingsIndexController::class)->name('landing-settings.index');
    Route::post('/landing-settings', [LandingSettingsActionController::class, 'upsert'])->name('landing-settings.upsert');

    Route::get('/about-page-settings', AboutPageSettingsIndexController::class)->name('about-page.index');
    Route::post('/about-page-settings', [AboutPageSettingsActionController::class, 'upsert'])->name('about-page.upsert');
    Route::post('/about-page-settings/upload-team-photo', [AboutPageSettingsActionController::class, 'uploadTeamPhoto'])->name('about-page.upload-team-photo');
    Route::post('/about-page-settings/upload-testimonial-photo', [AboutPageSettingsActionController::class, 'uploadTestimonialPhoto'])->name('about-page.upload-testimonial-photo');
    Route::post('/about-page-settings/upload-gallery', [AboutPageSettingsActionController::class, 'uploadGallery'])->name('about-page.upload-gallery');
    Route::post('/about-page-settings/delete-gallery', [AboutPageSettingsActionController::class, 'deleteGallery'])->name('about-page.delete-gallery');

    Route::get('/landing-banners', LandingBannersIndexController::class)->name('landing-banners.index');
    Route::post('/landing-banners', [LandingBannerActionController::class, 'store'])->name('landing-banners.store');
    Route::post('/landing-banners/{banner}', [LandingBannerActionController::class, 'update'])->name('landing-banners.update');
    Route::delete('/landing-banners/{banner}', [LandingBannerActionController::class, 'destroy'])->name('landing-banners.destroy');
    Route::post('/landing-banners/reorder', [LandingBannerActionController::class, 'reorder'])->name('landing-banners.reorder');

    Route::get('/configuracoes-gerais', SystemSettingsIndexController::class)->name('settings.index');
    Route::post('/configuracoes-gerais', [SystemSettingsActionController::class, 'update'])->name('settings.update');
    Route::post('/configuracoes-gerais/testar-email', [SystemSettingsActionController::class, 'testEmail'])->name('settings.test-email');
});
