<x-filament-panels::page>
    <form wire:submit="save">
        {{-- CONFIGURAÇÕES GERAIS --}}
        <x-filament::section>
            <x-slot name="heading">Configurações Gerais</x-slot>
            <x-slot name="description">Configurações básicas do Portal Elite Repasse.</x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">Nome do Portal</label>
                    <input wire:model="site_nome" type="text"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Elite Repasse">
                </div>
                <div class="flex items-center gap-3 pt-6">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input wire:model="aprovacao_automatica" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-primary-600 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all pointer-events-none"></div>
                        <span class="ml-3 text-base font-medium text-gray-700">Aprovação Automática de Clientes</span>
                    </label>
                </div>
            </div>
        </x-filament::section>

        {{-- GOOGLE reCAPTCHA --}}
        <x-filament::section class="mt-6">
            <x-slot name="heading">Google reCAPTCHA v3</x-slot>
            <x-slot name="description">Configure a proteção Anti-Robô no login. Obtenha as chaves em google.com/recaptcha.</x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">Site Key (pública)</label>
                    <input wire:model="google_recaptcha_site_key" type="text"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base font-mono"
                        placeholder="6Lcxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                </div>
                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">Secret Key (privada)</label>
                    <input wire:model="google_recaptcha_secret_key" type="password"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base font-mono">
                </div>
                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">Score Mínimo (0.0=bot | 1.0=humano)</label>
                    <input wire:model="google_recaptcha_score_minimo" type="number" step="0.1" min="0" max="1"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base"
                        placeholder="0.5">
                </div>
                <div class="flex items-center gap-3 pt-6">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input wire:model="google_recaptcha_ativo" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-primary-600 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all pointer-events-none"></div>
                        <span class="ml-3 text-base font-medium text-gray-700">Ativar reCAPTCHA no Login</span>
                    </label>
                </div>
            </div>
        </x-filament::section>

        {{-- GOOGLE MAPS & OAUTH --}}
        <x-filament::section class="mt-6">
            <x-slot name="heading">Google Maps e Login Social</x-slot>
            <x-slot name="description">API Key para geolocalização em contratos e credenciais para Login com Google.</x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">Google Maps Geocoding API Key</label>
                    <input wire:model="google_maps_api_key" type="password"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base font-mono"
                        placeholder="AIzaxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                </div>
                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">Google OAuth Client ID</label>
                    <input wire:model="google_oauth_client_id" type="text"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base font-mono"
                        placeholder="xxxxxxxxxxxxx.apps.googleusercontent.com">
                </div>
                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">Google OAuth Client Secret</label>
                    <input wire:model="google_oauth_client_secret" type="password"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base font-mono"
                        placeholder="GOCSPX-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                </div>
            </div>
        </x-filament::section>

        {{-- E-MAIL SMTP --}}
        <x-filament::section class="mt-6">
            <x-slot name="heading">Configurações de E-mail (SMTP)</x-slot>
            <x-slot name="description">Configure o servidor SMTP para envio de notificações por e-mail. Sem esta configuração, e-mails não serão enviados.</x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input wire:model="mail_smtp_ativo" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-primary-600 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all pointer-events-none"></div>
                        <span class="ml-3 text-base font-medium text-gray-700">Ativar envio de e-mail SMTP</span>
                    </label>
                </div>
                <div></div>

                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">Servidor SMTP (Host)</label>
                    <input wire:model="mail_smtp_host" type="text"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base font-mono"
                        placeholder="mail.eliterepasse.com.br">
                </div>
                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">Porta</label>
                    <input wire:model="mail_smtp_port" type="text"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base"
                        placeholder="465">
                </div>
                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">Usuário SMTP</label>
                    <input wire:model="mail_smtp_username" type="text"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base font-mono"
                        placeholder="noreply@eliterepasse.com.br">
                </div>
                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">Senha SMTP</label>
                    <input wire:model="mail_smtp_password" type="password"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base font-mono">
                </div>
                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">Criptografia</label>
                    <select wire:model="mail_smtp_encryption"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base">
                        <option value="ssl">SSL (porta 465)</option>
                        <option value="tls">TLS (porta 587)</option>
                        <option value="">Nenhuma</option>
                    </select>
                </div>
                <div></div>

                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">E-mail do Remetente</label>
                    <input wire:model="mail_from_address" type="email"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base"
                        placeholder="noreply@eliterepasse.com.br">
                </div>
                <div>
                    <label class="block text-base font-semibold text-gray-700 mb-1">Nome do Remetente</label>
                    <input wire:model="mail_from_name" type="text"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base"
                        placeholder="Elite Repasse">
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200">
                <button type="button" wire:click="testarEmail"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Enviar E-mail de Teste
                </button>
                <span class="ml-3 text-sm text-gray-500">Envia um e-mail de teste para o endereço do remetente</span>
            </div>
        </x-filament::section>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                class="btn-cta-md inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Salvar Configurações
            </button>
        </div>
    </form>
</x-filament-panels::page>
