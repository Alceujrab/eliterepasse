<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f62fe">

    <title>{{ config('admin_panel.title') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @php
        $manifestPath = public_path('build/manifest.json');
        $manifest = file_exists($manifestPath)
            ? json_decode(file_get_contents($manifestPath), true)
            : [];
        $hasAdminPanelCss = is_array($manifest) && array_key_exists('resources/css/admin-panel.css', $manifest);
    @endphp

    @if($hasAdminPanelCss)
        @vite(['resources/css/admin-panel.css', 'resources/js/app.js'])
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
<div class="admin-shell">
    <div class="admin-grid">
        <aside class="admin-sidebar">
            <div>
                <div class="admin-brand-title">Elite Repasse</div>
                <p class="admin-brand-subtitle">Admin v2</p>
            </div>

            <nav class="mt-7 grid gap-1.5">
                <a href="{{ route('admin.v2.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.v2.dashboard') ? 'is-active' : '' }}">
                    <span>Dashboard</span>
                </a>

                @foreach(config('admin_panel.modules', []) as $key => $module)
                    @continue($key === 'dashboard')
                    @php
                        $moduleUrl = $module['v2_path'] ?? route('admin.v2.module', $key);
                        $isModuleActive = request()->is(ltrim($module['v2_path'] ?? "painel-admin/modulo/{$key}", '/'))
                            || (request()->routeIs('admin.v2.module') && request()->route('module') === $key);
                    @endphp
                    <a href="{{ $moduleUrl }}" class="admin-nav-link {{ $isModuleActive ? 'is-active' : '' }}">
                        <span>{{ $module['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="mt-8 rounded-2xl border border-white/15 bg-white/10 p-4 text-xs leading-relaxed text-blue-100">
                Migração em execução: o admin legado continua ativo em
                <strong>/admin</strong> até concluir todos os módulos do novo painel.
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <h1 class="admin-headline">{{ $pageTitle ?? 'Painel Administrativo' }}</h1>
                <p class="admin-subline">{{ $pageSubtitle ?? config('admin_panel.subtitle') }}</p>

                <details class="admin-mobile-nav">
                    <summary>Menu rápido</summary>
                    <div class="admin-mobile-links">
                        <a href="{{ route('admin.v2.dashboard') }}" class="admin-btn-soft">Dashboard</a>
                        @foreach(config('admin_panel.modules', []) as $key => $module)
                            @continue($key === 'dashboard')
                            <a href="{{ $module['v2_path'] ?? route('admin.v2.module', $key) }}" class="admin-btn-soft">{{ $module['label'] }}</a>
                        @endforeach
                    </div>
                </details>
            </header>

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
