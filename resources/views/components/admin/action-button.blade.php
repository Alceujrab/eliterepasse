@props([
    'action',
    'method' => 'POST',
    'label',
    'variant' => 'primary', // primary | soft | danger
    'icon' => null,
    'confirm' => null,            // texto da pergunta de confirmacao; se null, submit direto
    'confirmDetail' => null,      // detalhe opcional abaixo do confirm
    'confirmLabel' => 'Sim, confirmar',
    'cancelLabel' => 'Cancelar',
    'disabled' => false,
    'loadingLabel' => 'Processando...',
    'reasonField' => null,        // se preenchido => render textarea com este name no popover
    'reasonRequired' => true,
    'reasonLabel' => 'Motivo',
    'hidden' => [],               // ['campo' => 'valor']
])

@php
    $methodUpper = strtoupper($method);
    $isSpoofed = ! in_array($methodUpper, ['GET', 'POST'], true);

    $variantClass = match($variant) {
        'danger' => 'admin-btn-danger',
        'soft'   => 'admin-btn-soft',
        default  => 'admin-btn-primary',
    };

    $needsConfirm = (bool) $confirm;
    $componentId = 'act_' . substr(md5($action . microtime(true) . random_int(1000, 9999)), 0, 8);
@endphp

<div
    x-data="{
        confirming: false,
        sending: false,
        reason: '',
        canSubmit() {
            @if($reasonField && $reasonRequired)
                return this.reason.trim().length >= 3;
            @else
                return true;
            @endif
        },
        submit(e) {
            if (this.sending) { e.preventDefault(); return; }
            this.sending = true;
        }
    }"
    class="relative inline-block"
>
    <form
        method="{{ $isSpoofed ? 'POST' : $methodUpper }}"
        action="{{ $action }}"
        @submit="submit($event)"
        class="inline-block"
    >
        @csrf
        @if($isSpoofed)
            @method($methodUpper)
        @endif

        @foreach($hidden as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach

        @if($reasonField)
            <input type="hidden" name="{{ $reasonField }}" :value="reason">
        @endif

        <button
            type="{{ $needsConfirm ? 'button' : 'submit' }}"
            @if($needsConfirm) @click="confirming = true" @endif
            :disabled="sending {{ $disabled ? '|| true' : '' }}"
            :class="sending ? 'opacity-70 cursor-wait' : ''"
            class="{{ $variantClass }} inline-flex items-center gap-2"
        >
            <svg x-show="sending" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
            </svg>
            <span x-text="sending ? '{{ $loadingLabel }}' : '{{ $label }}'">{{ $label }}</span>
        </button>

        @if($needsConfirm)
            <div
                x-show="confirming"
                x-cloak
                x-transition.opacity
                @keydown.escape.window="confirming = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4"
            >
                <div @click.outside="confirming = false" class="w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl">
                    <h3 class="text-base font-black text-slate-900">{{ $confirm }}</h3>
                    @if($confirmDetail)
                        <p class="mt-1 text-sm text-slate-600">{{ $confirmDetail }}</p>
                    @endif

                    @if($reasonField)
                        <label class="mt-4 block text-xs font-black uppercase tracking-[0.12em] text-slate-500">
                            {{ $reasonLabel }} @if($reasonRequired)<span class="text-rose-500">*</span>@endif
                        </label>
                        <textarea
                            x-model="reason"
                            rows="3"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none focus:border-blue-400"
                            placeholder="Descreva o motivo (minimo 3 caracteres)..."
                        ></textarea>
                    @endif

                    <div class="mt-5 flex flex-wrap justify-end gap-2">
                        <button type="button" @click="confirming = false" class="admin-btn-soft">{{ $cancelLabel }}</button>
                        <button
                            type="submit"
                            :disabled="!canSubmit() || sending"
                            :class="(!canSubmit() || sending) ? 'opacity-60 cursor-not-allowed' : ''"
                            class="{{ $variantClass }} inline-flex items-center gap-2"
                        >
                            <svg x-show="sending" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                            </svg>
                            {{ $confirmLabel }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </form>
</div>
