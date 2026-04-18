@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => true,
    'autoHide' => 6000, // 0 desativa
])

@php
    $palette = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'error'   => 'border-rose-200 bg-rose-50 text-rose-800',
        'info'    => 'border-blue-200 bg-blue-50 text-blue-800',
    ][$type] ?? 'border-slate-200 bg-slate-50 text-slate-800';

    $iconPath = [
        'success' => 'M5 13l4 4L19 7',
        'warning' => 'M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
        'error'   => 'M6 18L18 6M6 6l12 12',
        'info'    => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ][$type] ?? 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-init="@if($autoHide > 0) setTimeout(() => show = false, {{ (int) $autoHide }}) @endif"
    x-transition.opacity
    role="alert"
    aria-live="polite"
    class="mb-4 flex items-start gap-3 rounded-xl border px-4 py-3 text-sm font-semibold {{ $palette }}"
>
    <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
    </svg>
    <div class="min-w-0 flex-1">
        @if($title)
            <p class="font-black">{{ $title }}</p>
        @endif
        <div class="{{ $title ? 'mt-0.5 font-semibold opacity-90' : '' }}">{{ $slot }}</div>
    </div>
    @if($dismissible)
        <button type="button" @click="show = false" class="-m-1 rounded-md p-1 hover:bg-black/5" aria-label="Fechar">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
