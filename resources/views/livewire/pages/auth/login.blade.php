<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    @if(config('services.recaptcha.site_key') && \App\Models\SystemSetting::get('google_recaptcha_ativo'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    @endif

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-[#1a3a5c] to-[#1e4f8a] flex items-center justify-center text-white text-2xl mb-4 shadow-lg">
            🚗
        </div>
        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Bem-vindo de volta!</h2>
        <p class="mt-2 text-sm text-gray-500 font-medium">Acesse o Portal do Lojista com seu e-mail e senha</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form 
        x-data="{
            submitting: false,
            submitForm() {
                this.submitting = true;
                const siteKey = '{{ (config('services.recaptcha.site_key') && \App\Models\SystemSetting::get('google_recaptcha_ativo')) ? config('services.recaptcha.site_key') : '' }}';
                
                if (siteKey) {
                    grecaptcha.ready(() => {
                        grecaptcha.execute(siteKey, {action: 'login'}).then((token) => {
                            @this.set('form.recaptcha_token', token);
                            @this.login();
                            this.submitting = false;
                        });
                    });
                } else {
                    @this.login();
                    this.submitting = false;
                }
            }
        }"
        @submit.prevent="submitForm"
        class="space-y-5"
    >
        {{-- Login --}}
        <div>
            <label for="login" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">E-mail ou CPF</label>
            <div class="relative">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <input wire:model="form.login" id="login"
                    class="block w-full bg-[#f8fafc] border border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent shadow-sm pl-11 pr-4 py-3.5 text-sm transition hover:bg-white hover:border-gray-300"
                    type="text" name="login" placeholder="seu@email.com" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('form.login')" class="mt-1.5" />
        </div>

        {{-- Senha --}}
        <div x-data="{ show: false }">
            <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Senha</label>
            <div class="relative">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input wire:model="form.password" id="password"
                    class="block w-full bg-[#f8fafc] border border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent shadow-sm pl-11 pr-12 py-3.5 text-sm transition hover:bg-white hover:border-gray-300"
                    :type="show ? 'text' : 'password'" name="password" placeholder="••••••••" required autocomplete="current-password" />
                <button type="button" @click="show = !show"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1.5" />
        </div>

        {{-- Esqueceu senha --}}
        <div class="flex items-center justify-end">
            @if (Route::has('password.request'))
                <a class="text-xs font-bold text-[#1a3a5c] hover:text-orange-500 transition" href="{{ route('password.request') }}" wire:navigate>
                    Esqueceu a senha?
                </a>
            @endif
        </div>

        {{-- Botão --}}
        <button type="submit"
            x-bind:disabled="submitting"
            x-bind:class="{ 'opacity-70 cursor-not-allowed': submitting }"
            class="w-full flex items-center justify-center gap-2 py-4 px-4 rounded-xl text-white font-black text-base bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 shadow-lg shadow-orange-500/20 transition-all transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
            <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            <svg x-show="submitting" x-cloak class="w-5 h-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-text="submitting ? 'Autenticando...' : 'Entrar no Portal'"></span>
        </button>

        {{-- Divider --}}
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
            <div class="relative flex justify-center"><span class="bg-white px-4 text-xs font-bold text-gray-400 uppercase tracking-widest">ou</span></div>
        </div>

        {{-- Google OAuth --}}
        <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 py-3.5 px-4 bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 rounded-xl font-bold text-sm text-gray-700 shadow-sm transition">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                <path d="M1 1h22v22H1z" fill="none"/>
            </svg>
            Entrar com o Google
        </a>

        {{-- Cadastrar --}}
        @if (Route::has('register'))
            <a href="{{ route('register') }}" wire:navigate
                class="w-full flex items-center justify-center gap-2 py-3.5 px-4 mt-3 rounded-xl font-bold text-sm text-[#1a3a5c] bg-[#f0f4f8] hover:bg-[#e2e8f0] border border-gray-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Cadastrar minha empresa
            </a>
        @endif

        {{-- Help --}}
        <div class="text-center mt-6">
            <p class="text-xs text-gray-400">
                Problemas para acessar?
                <a href="mailto:suporte@eliterepasse.com.br" class="font-bold text-gray-500 hover:text-orange-500 transition">Fale conosco</a>
            </p>
        </div>

        {{-- reCAPTCHA badge --}}
        @if(config('services.recaptcha.site_key') && \App\Models\SystemSetting::get('google_recaptcha_ativo'))
            <div class="mt-4 flex items-center justify-center gap-2 text-[10px] text-gray-400 leading-tight text-center">
                <svg class="w-4 h-4 flex-shrink-0 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Protegido por reCAPTCHA Google.
                    <a href="https://policies.google.com/privacy" target="_blank" rel="noopener" class="underline hover:text-gray-500">Privacidade</a> &
                    <a href="https://policies.google.com/terms" target="_blank" rel="noopener" class="underline hover:text-gray-500">Termos</a>.
                </span>
            </div>
        @endif
    </form>
</div>
