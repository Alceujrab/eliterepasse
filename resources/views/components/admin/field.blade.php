@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => '',
    'hint' => null,
    'mask' => null,    // ex: '000.000.000-00' ou 'cpf'/'cnpj'/'placa'/'cep'/'telefone'/'dinheiro'
    'maxlength' => null,
    'min' => null,
    'max' => null,
    'step' => null,
    'autocomplete' => null,
    'inputClass' => 'admin-input',
    'monospace' => false,
])

@php
    $hasError = $errors->has($name);
    $oldValue = old($name, $value);

    // Plugin @alpinejs/mask usa: 9 = dígito, a = letra, * = alphanumérico
    $maskMap = [
        'cpf'       => '999.999.999-99',
        'cnpj'      => '99.999.999/9999-99',
        'cep'       => '99999-999',
        'telefone'  => ['(99) 9999-9999', '(99) 99999-9999'],
        'placa'     => 'aaa-9*99',
        'renavam'   => '99999999999',
        'chassi'    => '*****************', // 17 chars alphanumerico
        'fipe'      => '999999-9',
        'dinheiro'  => null, // tratado via x-mask:dynamic abaixo
    ];

    $resolvedMask = $mask;
    if ($mask && isset($maskMap[$mask])) {
        $resolvedMask = $maskMap[$mask];
    }

    $alpineAttr = '';
    if ($resolvedMask) {
        if (is_array($resolvedMask)) {
            $alpineAttr = "x-mask:dynamic=\"\$input.length > 14 ? '" . $resolvedMask[1] . "' : '" . $resolvedMask[0] . "'\"";
        } elseif ($mask === 'dinheiro') {
            $alpineAttr = "x-mask:dynamic=\"\$money(\$input, ',')\"";
        } else {
            $alpineAttr = "x-mask=\"{$resolvedMask}\"";
        }
    }

    $monoClass = $monospace ? ' font-mono' : '';
    if (in_array($mask, ['placa', 'chassi'], true)) {
        $monoClass .= ' uppercase';
    }
@endphp

<div @if($alpineAttr) x-data="{}" @endif>
    <label for="{{ $name }}" class="admin-field-label">
        {{ $label }} @if($required)<span class="text-rose-500" aria-hidden="true">*</span>@endif
    </label>

    @if($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            @if($required) required aria-required="true" @endif
            @if($maxlength) maxlength="{{ $maxlength }}" @endif
            placeholder="{{ $placeholder }}"
            {!! $attributes->get('rows') ? 'rows="' . $attributes->get('rows') . '"' : 'rows="4"' !!}
            class="{{ $inputClass }}{{ $monoClass }} {{ $hasError ? 'border-rose-400 focus:border-rose-500' : '' }}"
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
            @if($hasError) aria-describedby="{{ $name }}-error" @elseif($hint) aria-describedby="{{ $name }}-hint" @endif
        >{{ $oldValue }}</textarea>
    @else
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $oldValue }}"
            @if($required) required aria-required="true" @endif
            @if($maxlength) maxlength="{{ $maxlength }}" @endif
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if(! is_null($min)) min="{{ $min }}" @endif
            @if(! is_null($max)) max="{{ $max }}" @endif
            @if($step) step="{{ $step }}" @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            {!! $alpineAttr !!}
            class="{{ $inputClass }}{{ $monoClass }} {{ $hasError ? 'border-rose-400 focus:border-rose-500' : '' }}"
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
            @if($hasError) aria-describedby="{{ $name }}-error" @elseif($hint) aria-describedby="{{ $name }}-hint" @endif
        >
    @endif

    @if($hasError)
        <p id="{{ $name }}-error" class="mt-1 flex items-start gap-1 text-xs font-semibold text-rose-600">
            <svg class="mt-0.5 h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <span>{{ $errors->first($name) }}</span>
        </p>
    @elseif($hint)
        <p id="{{ $name }}-hint" class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
