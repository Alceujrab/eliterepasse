{{-- Renderiza automaticamente as session flashes padrao do Admin v2.
     Consome (pull) os valores da sessao para evitar duplicacao com blocos legados nas views. --}}
@php
    $flashSuccess = session()->pull('admin_success');
    $flashWarning = session()->pull('admin_warning');
    $flashError = session()->pull('admin_error');
    $flashInfo = session()->pull('admin_info');
@endphp

@if($flashSuccess)
    <x-admin.alert type="success">{{ $flashSuccess }}</x-admin.alert>
@endif

@if($flashWarning)
    <x-admin.alert type="warning">{{ $flashWarning }}</x-admin.alert>
@endif

@if($flashError)
    <x-admin.alert type="error">{{ $flashError }}</x-admin.alert>
@endif

@if($flashInfo)
    <x-admin.alert type="info">{{ $flashInfo }}</x-admin.alert>
@endif

@if($errors->any() && ! isset($hideValidationErrors))
    <x-admin.alert type="error" title="Corrija os campos destacados antes de continuar.">
        <ul class="mt-1 list-disc pl-5 space-y-0.5">
            @foreach($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </x-admin.alert>
@endif
