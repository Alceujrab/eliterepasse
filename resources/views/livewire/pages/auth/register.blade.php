<?php

use App\Models\User;
use App\Models\Company;
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

    // Step wizard
    public int $step = 1;

    public function nextStep(): void  { $this->step = min($this->step + 1, 4); }
    public function prevStep(): void  { $this->step = max($this->step - 1, 1); }

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
        $validated['status']   = 'pendente';

        event(new Registered($user = User::create($validated)));

        // Criar a empresa e vincular ao usuário
        $company = Company::create([
            'razao_social'       => $this->razao_social,
            'cnpj'               => $this->cnpj,
            'whatsapp'           => $this->phone,
            'inscricao_estadual' => $this->inscricao_estadual ?: null,
            'address'            => trim(($this->logradouro ? $this->logradouro . ', ' : '') . $this->numero),
            'city'               => $this->cidade ?: null,
            'state'              => $this->estado ?: null,
            'zipcode'            => $this->cep ?: null,
        ]);
        $user->companies()->attach($company);

        // Notificar admins sobre novo cadastro (email + WhatsApp)
        app(\App\Services\NotificationService::class)->novoCadastroParaAdmin($user);

        session()->flash('cadastro_pendente', true);

        $this->redirect(route('login'), navigate: true);
    }
}; ?>

<div>
    {{-- Header --}}
    <div class="text-center mb-6">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-[#1a3a5c] to-[#1e4f8a] flex items-center justify-center text-white text-2xl mb-4 shadow-lg">
            🏢
        </div>
        <h2 class="text-xl font-black text-gray-900 tracking-tight">Cadastre sua empresa</h2>
        <p class="mt-1 text-sm text-gray-500">Após análise da equipe, seu acesso será liberado</p>
    </div>

    {{-- Step indicators --}}
    <div class="flex items-center justify-center gap-1.5 mb-6">
        @foreach(['Empresa', 'Responsável', 'Endereço', 'Senha'] as $idx => $label)
            @php $s = $idx + 1; @endphp
            <button wire:click="$set('step', {{ $s }})"
                class="flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold transition
                    {{ $step === $s ? 'bg-[#1a3a5c] text-white' : ($step > $s ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400') }}">
                @if($step > $s) ✓ @else {{ $s }} @endif
                <span class="hidden sm:inline">{{ $label }}</span>
            </button>
        @endforeach
    </div>

    <form wire:submit="register">

        {{-- Step 1: Empresa --}}
        @if($step === 1)
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Razão Social *</label>
                    <input wire:model="razao_social" type="text" placeholder="Empresa Ltda"
                        class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition" required>
                    <x-input-error :messages="$errors->get('razao_social')" class="mt-1"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nome Fantasia</label>
                    <input wire:model="nome_fantasia" type="text" placeholder="Nome para exibição"
                        class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">CNPJ *</label>
                        <input wire:model="cnpj" type="text" x-mask="99.999.999/9999-99" placeholder="00.000.000/0000-00"
                            class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition" required>
                        <x-input-error :messages="$errors->get('cnpj')" class="mt-1"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Inscrição Estadual</label>
                        <input wire:model="inscricao_estadual" type="text" placeholder="Isento ou nº"
                            class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition">
                    </div>
                </div>
                <button type="button" wire:click="nextStep"
                    class="w-full py-3.5 rounded-xl bg-[#1a3a5c] text-white font-black text-sm hover:bg-[#0f2d4e] transition shadow-sm">
                    Próximo →
                </button>
            </div>
        @endif

        {{-- Step 2: Responsável --}}
        @if($step === 2)
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nome Completo *</label>
                    <input wire:model="name" type="text" placeholder="Seu nome"
                        class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition" required>
                    <x-input-error :messages="$errors->get('name')" class="mt-1"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">E-mail *</label>
                    <input wire:model="email" type="email" placeholder="seu@empresa.com.br"
                        class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition" required>
                    <x-input-error :messages="$errors->get('email')" class="mt-1"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">WhatsApp</label>
                    <input wire:model="phone" type="text" x-mask="(99) 9 9999-9999" placeholder="(00) 9 0000-0000"
                        class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition">
                </div>
                <div class="flex gap-3">
                    <button type="button" wire:click="prevStep" class="px-5 py-3.5 rounded-xl bg-gray-100 text-gray-600 font-bold text-sm hover:bg-gray-200 transition">← Voltar</button>
                    <button type="button" wire:click="nextStep" class="flex-1 py-3.5 rounded-xl bg-[#1a3a5c] text-white font-black text-sm hover:bg-[#0f2d4e] transition shadow-sm">Próximo →</button>
                </div>
            </div>
        @endif

        {{-- Step 3: Endereço --}}
        @if($step === 3)
            <div class="space-y-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">CEP</label>
                        <input wire:model.blur="cep" wire:change="buscarCep" type="text" x-mask="99999-999" placeholder="00000-000"
                            class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Logradouro</label>
                        <input wire:model="logradouro" type="text"
                            class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Número</label>
                        <input wire:model="numero" type="text"
                            class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Complemento</label>
                        <input wire:model="complemento" type="text"
                            class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Bairro</label>
                        <input wire:model="bairro" type="text"
                            class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Cidade</label>
                        <input wire:model="cidade" type="text"
                            class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Estado</label>
                        <select wire:model="estado"
                            class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition">
                            <option value="">UF</option>
                            @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                <option value="{{ $uf }}" @selected($estado === $uf)>{{ $uf }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="button" wire:click="prevStep" class="px-5 py-3.5 rounded-xl bg-gray-100 text-gray-600 font-bold text-sm hover:bg-gray-200 transition">← Voltar</button>
                    <button type="button" wire:click="nextStep" class="flex-1 py-3.5 rounded-xl bg-[#1a3a5c] text-white font-black text-sm hover:bg-[#0f2d4e] transition shadow-sm">Próximo →</button>
                </div>
            </div>
        @endif

        {{-- Step 4: Senha --}}
        @if($step === 4)
            <div class="space-y-4">
                <div x-data="{ show: false }">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Senha *</label>
                    <div class="relative">
                        <input wire:model="password" :type="show ? 'text' : 'password'" placeholder="Mínimo 8 caracteres"
                            class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 pr-12 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition" required>
                        <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1"/>
                </div>
                <div x-data="{ show: false }">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Confirmar Senha *</label>
                    <div class="relative">
                        <input wire:model="password_confirmation" :type="show ? 'text' : 'password'" placeholder="Repita a senha"
                            class="block w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-3 pr-12 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:bg-white hover:border-gray-300 transition" required>
                        <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Info box --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <p class="text-xs text-blue-700 font-semibold">📋 Após o envio, nossa equipe analisará seu cadastro e enviará um e-mail quando o acesso for liberado.</p>
                </div>

                <div class="flex gap-3">
                    <button type="button" wire:click="prevStep" class="px-5 py-3.5 rounded-xl bg-gray-100 text-gray-600 font-bold text-sm hover:bg-gray-200 transition">← Voltar</button>
                    <button type="submit"
                        class="flex-1 py-3.5 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white font-black text-sm hover:opacity-90 transition shadow-lg shadow-orange-500/20 flex items-center justify-center gap-2">
                        ✅ Enviar Cadastro
                    </button>
                </div>
            </div>
        @endif
    </form>

    {{-- Login link --}}
    <div class="mt-6 text-center">
        <p class="text-xs text-gray-500">
            Já tem conta?
            <a href="{{ route('login') }}" wire:navigate class="font-bold text-orange-500 hover:text-orange-600 transition">Fazer login</a>
        </p>
    </div>
</div>
