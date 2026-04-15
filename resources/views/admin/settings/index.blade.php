@extends('admin.layouts.app')

@php
    $pageTitle = 'Configurações Gerais';
    $pageSubtitle = 'Centralize SMTP, reCAPTCHA, Google e Gemini no Admin v2.';

    $current = fn (string $key, mixed $default = '') => old($key, $settings[$key] ?? $default);
    $portalName = $current('site_nome', config('app.name', 'Elite Repasse'));
    $smtpEnabled = filter_var($current('mail_smtp_ativo', false), FILTER_VALIDATE_BOOL);
    $recaptchaEnabled = filter_var($current('google_recaptcha_ativo', false), FILTER_VALIDATE_BOOL);
    $autoApproval = filter_var($current('aprovacao_automatica', false), FILTER_VALIDATE_BOOL);
@endphp

@section('content')
    @if(session('admin_success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('admin_success') }}</div>
    @endif

    @if(session('admin_error'))
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ session('admin_error') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <section class="admin-metrics-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Portal</p>
            <p class="admin-metric-value text-[1.35rem]">{{ $portalName }}</p>
            <p class="admin-metric-footnote">Nome institucional em uso</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">SMTP</p>
            <p class="admin-metric-value">{{ $smtpEnabled ? 'ON' : 'OFF' }}</p>
            <p class="admin-metric-footnote">Envio de e-mails transacionais</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">reCAPTCHA</p>
            <p class="admin-metric-value">{{ $recaptchaEnabled ? 'ON' : 'OFF' }}</p>
            <p class="admin-metric-footnote">Proteção do login público</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Aprovação automática</p>
            <p class="admin-metric-value">{{ $autoApproval ? 'ON' : 'OFF' }}</p>
            <p class="admin-metric-footnote">Entrada automática de clientes</p>
        </article>
    </section>

    <section class="mt-6 admin-detail-grid">
        <div class="admin-stack">
            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <span class="admin-tag admin-tag-new">settings v2</span>
                        <h2 class="mt-3 admin-section-title">Configuração do sistema</h2>
                        <p class="admin-section-note">Edite credenciais sensíveis e parâmetros operacionais sem depender do painel legado.</p>
                    </div>
                </div>

                <form id="system-settings-form" method="POST" action="{{ route('admin.v2.settings.update') }}" class="mt-5 admin-stack">
                    @csrf

                    <section class="admin-card !p-4">
                        <h3 class="admin-section-title">Portal e aprovação</h3>
                        <div class="mt-4 grid gap-4 xl:grid-cols-2">
                            <div class="admin-info-card xl:col-span-2">
                                <label class="admin-detail-label">Nome do portal</label>
                                <input type="text" name="site_nome" value="{{ $current('site_nome', config('app.name', 'Elite Repasse')) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="Elite Repasse">
                            </div>
                            <label class="admin-info-card flex items-center justify-between gap-4 xl:col-span-2">
                                <div>
                                    <div class="admin-detail-label">Aprovação automática</div>
                                    <p class="mt-1 text-sm font-medium text-slate-500">Libera novos clientes sem revisão manual do comercial.</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="hidden" name="aprovacao_automatica" value="0">
                                    <input type="checkbox" name="aprovacao_automatica" value="1" class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500" @checked($autoApproval)>
                                    <span class="admin-row-meta">{{ $autoApproval ? 'Ativo' : 'Desligado' }}</span>
                                </div>
                            </label>
                        </div>
                    </section>

                    <section class="admin-card !p-4">
                        <h3 class="admin-section-title">Google reCAPTCHA v3</h3>
                        <div class="mt-4 grid gap-4 xl:grid-cols-2">
                            <div class="admin-info-card">
                                <label class="admin-detail-label">Site key</label>
                                <input type="text" name="google_recaptcha_site_key" value="{{ $current('google_recaptcha_site_key') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="6Lc...">
                            </div>
                            <div class="admin-info-card">
                                <label class="admin-detail-label">Secret key</label>
                                <input type="password" name="google_recaptcha_secret_key" value="{{ $current('google_recaptcha_secret_key') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="6Lc...">
                            </div>
                            <div class="admin-info-card">
                                <label class="admin-detail-label">Score mínimo</label>
                                <input type="number" name="google_recaptcha_score_minimo" min="0" max="1" step="0.1" value="{{ $current('google_recaptcha_score_minimo', '0.5') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                            </div>
                            <label class="admin-info-card flex items-center justify-between gap-4">
                                <div>
                                    <div class="admin-detail-label">Ativar no login</div>
                                    <p class="mt-1 text-sm font-medium text-slate-500">Protege a entrada pública contra automação.</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="hidden" name="google_recaptcha_ativo" value="0">
                                    <input type="checkbox" name="google_recaptcha_ativo" value="1" class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500" @checked($recaptchaEnabled)>
                                    <span class="admin-row-meta">{{ $recaptchaEnabled ? 'Ativo' : 'Desligado' }}</span>
                                </div>
                            </label>
                        </div>
                    </section>

                    <section class="admin-card !p-4">
                        <h3 class="admin-section-title">Google e IA</h3>
                        <div class="mt-4 grid gap-4 xl:grid-cols-2">
                            <div class="admin-info-card xl:col-span-2">
                                <label class="admin-detail-label">Google Maps API key</label>
                                <input type="password" name="google_maps_api_key" value="{{ $current('google_maps_api_key') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                            </div>
                            <div class="admin-info-card">
                                <label class="admin-detail-label">Google OAuth Client ID</label>
                                <input type="text" name="google_oauth_client_id" value="{{ $current('google_oauth_client_id') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                            </div>
                            <div class="admin-info-card">
                                <label class="admin-detail-label">Google OAuth Client Secret</label>
                                <input type="password" name="google_oauth_client_secret" value="{{ $current('google_oauth_client_secret') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                            </div>
                            <div class="admin-info-card xl:col-span-2">
                                <label class="admin-detail-label">Gemini API key</label>
                                <input type="password" name="gemini_api_key" value="{{ $current('gemini_api_key') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                            </div>
                        </div>
                    </section>

                    <section class="admin-card !p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h3 class="admin-section-title">SMTP</h3>
                                <p class="admin-section-note">Essas credenciais sustentam os e-mails transacionais do portal.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="mail_smtp_ativo" value="0">
                                <input type="checkbox" name="mail_smtp_ativo" value="1" class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500" @checked($smtpEnabled)>
                                <span class="admin-row-meta">{{ $smtpEnabled ? 'SMTP ativo' : 'SMTP inativo' }}</span>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 xl:grid-cols-2">
                            <div class="admin-info-card">
                                <label class="admin-detail-label">Host SMTP</label>
                                <input type="text" name="mail_smtp_host" value="{{ $current('mail_smtp_host') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="mail.eliterepasse.com.br">
                            </div>
                            <div class="admin-info-card">
                                <label class="admin-detail-label">Porta</label>
                                <input type="text" name="mail_smtp_port" value="{{ $current('mail_smtp_port', '465') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="465">
                            </div>
                            <div class="admin-info-card">
                                <label class="admin-detail-label">Usuário SMTP</label>
                                <input type="text" name="mail_smtp_username" value="{{ $current('mail_smtp_username') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                            </div>
                            <div class="admin-info-card">
                                <label class="admin-detail-label">Senha SMTP</label>
                                <input type="password" name="mail_smtp_password" value="{{ $current('mail_smtp_password') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                            </div>
                            <div class="admin-info-card">
                                <label class="admin-detail-label">Criptografia</label>
                                <select name="mail_smtp_encryption" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                                    <option value="ssl" @selected($current('mail_smtp_encryption', 'ssl') === 'ssl')>SSL</option>
                                    <option value="tls" @selected($current('mail_smtp_encryption', 'ssl') === 'tls')>TLS</option>
                                    <option value="" @selected($current('mail_smtp_encryption', 'ssl') === '')>Nenhuma</option>
                                </select>
                            </div>
                            <div class="admin-info-card">
                                <label class="admin-detail-label">E-mail do remetente</label>
                                <input type="email" name="mail_from_address" value="{{ $current('mail_from_address') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="noreply@eliterepasse.com.br">
                            </div>
                            <div class="admin-info-card xl:col-span-2">
                                <label class="admin-detail-label">Nome do remetente</label>
                                <input type="text" name="mail_from_name" value="{{ $current('mail_from_name', $portalName) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
                            </div>
                        </div>
                    </section>

                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="admin-btn-primary">Salvar configurações</button>
                        <button type="submit" formaction="{{ route('admin.v2.settings.test-email') }}" class="admin-btn-soft">Enviar e-mail de teste</button>
                    </div>
                </form>
            </section>
        </div>

        <aside class="admin-stack">
            <section class="admin-card">
                <span class="admin-tag admin-tag-new">diagnostico</span>
                <h2 class="mt-3 admin-section-title">Estado atual</h2>
                <div class="mt-4 admin-stack">
                    <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="admin-row-title">Portal</div>
                        <div class="admin-row-meta mt-1">{{ $portalName }}</div>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="admin-row-title">Google OAuth</div>
                        <div class="admin-row-meta mt-1">{{ $current('google_oauth_client_id') ?: 'Não configurado' }}</div>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="admin-row-title">Remetente SMTP</div>
                        <div class="admin-row-meta mt-1">{{ $current('mail_from_address') ?: 'Não configurado' }}</div>
                    </article>
                </div>
            </section>

            <section class="admin-card">
                <h2 class="admin-section-title">Teste de e-mail</h2>
                <p class="mt-2 text-sm font-medium text-slate-500">Use o botão dentro do formulário principal para testar com os valores atuais, antes de salvar.</p>
            </section>
        </aside>
    </section>
@endsection