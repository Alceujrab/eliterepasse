<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    // Dados pessoais
    public string $name = '';
    public string $email = '';
    public string $phone = '';

    // Dados PJ
    public string $razao_social = '';
    public string $nome_fantasia = '';
    public string $cnpj = '';
    public string $inscricao_estadual = '';

    // Endereço
    public string $cep = '';
    public string $logradouro = '';
    public string $numero = '';
    public string $complemento = '';
    public string $bairro = '';
    public string $cidade = '';
    public string $estado = '';

    // Senha
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Busca endereço via ViaCEP (chamado via Alpine.js no blur do CEP)
     */
    public function buscarCep(): void
    {
        $cep = preg_replace('/\D/', '', $this->cep);

        if (strlen($cep) !== 8) return;

        try {
            $response = \Illuminate\Support\Facades\Http::get("https://viacep.com.br/ws/{$cep}/json/");
            $data = $response->json();

            if ($response->ok() && !isset($data['erro'])) {
                $this->logradouro = $data['logradouro'] ?? '';
                $this->bairro     = $data['bairro'] ?? '';
                $this->cidade     = $data['localidade'] ?? '';
                $this->estado     = $data['uf'] ?? '';
            }
        } catch (\Exception $e) {}
    }

    public function register(): void
    {
        $validated = $this->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'razao_social'  => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['nullable', 'string', 'max:255'],
            'cnpj'          => ['required', 'string', 'max:18', 'unique:users'],
            'inscricao_estadual' => ['nullable', 'string', 'max:30'],
            'cep'           => ['nullable', 'string', 'max:9'],
            'logradouro'    => ['nullable', 'string', 'max:255'],
            'numero'        => ['nullable', 'string', 'max:10'],
            'complemento'   => ['nullable', 'string', 'max:100'],
            'bairro'        => ['nullable', 'string', 'max:100'],
            'cidade'        => ['nullable', 'string', 'max:100'],
            'estado'        => ['nullable', 'string', 'max:2'],
            'password'      => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ], [], [
            'razao_social' => 'razão social',
            'cnpj'         => 'CNPJ',
            'email'        => 'e-mail',
            'password'     => 'senha',
            'password_confirmation' => 'confirmação de senha',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status']   = 'pendente'; // aguarda aprovação

        event(new Registered($user = User::create($validated)));

        // NÃO faz auto-login: usuário deve aguardar aprovação
        session()->flash('cadastro_pendente', true);

        $this->redirect(route('login'), navigate: true);
    }
}; ?>

<div>
    <div class="text-left mb-8">
        <h2 class="text-[24px] leading-[30px] font-black text-gray-800 tracking-tight">Cadastre sua empresa</h2>
        <p class="mt-2 text-[14px] text-gray-500 font-medium">Preencha os dados abaixo. Após análise da equipe, seu acesso será liberado.</p>
    </div>

    <form wire:submit="register" class="space-y-5">

        {{-- Dados da Empresa --}}
        <div class="border border-gray-200 rounded-xl p-5 space-y-4 bg-gray-50/50">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">🏢 Dados da Empresa</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Razão Social *</label>
                    <input wire:model="razao_social" type="text" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Empresa Ltda" required>
                    <x-input-error :messages="$errors->get('razao_social')" class="mt-1"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nome Fantasia</label>
                    <input wire:model="nome_fantasia" type="text" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500" placeholder="Nome Fantasia">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">CNPJ *</label>
                    <input wire:model="cnpj" type="text" x-mask="99.999.999/9999-99" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-500" placeholder="00.000.000/0000-00" required>
                    <x-input-error :messages="$errors->get('cnpj')" class="mt-1"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Inscrição Estadual</label>
                    <input wire:model="inscricao_estadual" type="text" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm" placeholder="Isento ou número">
                </div>
            </div>
        </div>

        {{-- Dados do Responsável --}}
        <div class="border border-gray-200 rounded-xl p-5 space-y-4 bg-gray-50/50">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">👤 Responsável</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nome Completo *</label>
                    <input wire:model="name" type="text" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500" required>
                    <x-input-error :messages="$errors->get('name')" class="mt-1"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">E-mail *</label>
                    <input wire:model="email" type="email" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500" required>
                    <x-input-error :messages="$errors->get('email')" class="mt-1"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">WhatsApp</label>
                    <input wire:model="phone" type="text" x-mask="(99) 9 9999-9999" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm" placeholder="(00) 9 0000-0000">
                </div>
            </div>
        </div>

        {{-- Endereço com busca automática CEP --}}
        <div class="border border-gray-200 rounded-xl p-5 space-y-4 bg-gray-50/50">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">📍 Endereço</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">CEP</label>
                    <input wire:model.blur="cep" wire:change="buscarCep" type="text" x-mask="99999-999"
                        class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-mono"
                        placeholder="00000-000">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Logradouro</label>
                    <input wire:model="logradouro" type="text" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Número</label>
                    <input wire:model="numero" type="text" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Complemento</label>
                    <input wire:model="complemento" type="text" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bairro</label>
                    <input wire:model="bairro" type="text" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Cidade</label>
                    <input wire:model="cidade" type="text" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                    <select wire:model="estado" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm bg-white">
                        <option value="">UF</option>
                        @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                            <option value="{{ $uf }}" @selected($estado === $uf)>{{ $uf }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Senha com olhinho --}}
        <div class="border border-gray-200 rounded-xl p-5 space-y-4 bg-gray-50/50">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">🔒 Senha de Acesso</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div x-data="{ show: false }">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Senha *</label>
                    <div class="relative">
                        <input wire:model="password" :type="show ? 'text' : 'password'" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-12 text-sm focus:ring-2 focus:ring-orange-500" placeholder="Mínimo 8 caracteres" required>
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1"/>
                </div>
                <div x-data="{ show: false }">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirmar Senha *</label>
                    <div class="relative">
                        <input wire:model="password_confirmation" :type="show ? 'text' : 'password'" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-12 text-sm focus:ring-2 focus:ring-orange-500" placeholder="Repita a senha" required>
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Termos e Submit --}}
        <div class="flex flex-col gap-4 pt-2">
            <button type="submit"
                class="w-full flex items-center justify-center gap-2 py-4 px-4 rounded-xl text-white font-black text-[16px] bg-orange-500 hover:bg-orange-600 shadow-lg shadow-orange-500/20 transition-all transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Enviar Cadastro para Análise
            </button>

            <p class="text-center text-sm text-gray-500">
                Já tem conta?
                <a href="{{ route('login') }}" wire:navigate class="text-orange-600 font-bold hover:underline">Fazer login</a>
            </p>
        </div>
    </form>
</div>
