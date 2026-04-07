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
    <div class="text-left mb-8">
        <h2 class="text-[26px] leading-[32px] font-black text-gray-800 tracking-tight">Olá, bom ter você no Portal do Lojista!</h2>
        <p class="mt-3 text-[15px] text-gray-500 font-medium">Para acessar a sua conta, insira o e-mail ou CPF e senha cadastrados:</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <div>
            <label for="login" class="block text-sm font-bold text-gray-700 mb-1.5">Login</label>
            <input wire:model="form.login" id="login" class="block w-full bg-[#f8fafc] border border-gray-300 text-gray-900 text-base rounded-md focus:ring-2 focus:ring-primary focus:border-primary shadow-sm px-4 py-3 transition hover:bg-white" type="text" name="login" placeholder="E-mail ou CPF" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.login')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-bold text-gray-700 mb-1.5">Senha</label>
            <div class="relative">
                <input wire:model="form.password" id="password" class="block w-full bg-[#f8fafc] border border-gray-300 text-gray-900 text-base rounded-md focus:ring-2 focus:ring-primary focus:border-primary shadow-sm px-4 py-3 transition hover:bg-white" type="password" name="password" placeholder="Sua senha" required autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end pt-2">
            @if (Route::has('password.request'))
                <a class="text-sm font-bold text-primary hover:text-blue-800 transition underline decoration-2 underline-offset-4" href="{{ route('password.request') }}" wire:navigate>
                    Esqueceu sua senha?
                </a>
            @endif
        </div>

        <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-lg shadow-lg shadow-orange_cta/20 text-[17px] font-black text-white bg-orange_cta hover:bg-[#e06512] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange_cta transition-all transform hover:-translate-y-0.5 mt-4">
            Entrar
        </button>

        <div class="text-center mt-8 cursor-pointer group">
            <p class="text-sm text-gray-500 font-bold flex items-center justify-center gap-2 group-hover:text-gray-800 transition">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Problemas para acessar? <span class="underline decoration-1 underline-offset-4 decoration-gray-400 group-hover:decoration-gray-800">Clique aqui e confira o tutorial</span>
            </p>
        </div>
    </form>
</div>
