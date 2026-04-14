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
        <style>
            :root {
                --admin-bg: #eef3fb;
                --admin-surface: #ffffff;
                --admin-text: #122137;
                --admin-muted: #5f6f86;
                --admin-border: #d9e3f1;
            }

            body {
                background: radial-gradient(circle at 10% -20%, #d7e8ff 0, transparent 45%),
                radial-gradient(circle at 90% -10%, #ffe8d4 0, transparent 35%), var(--admin-bg);
                color: var(--admin-text);
                font-family: 'Sora', 'Segoe UI', sans-serif;
            }

            .admin-grid { display: grid; min-height: 100vh; grid-template-columns: 290px 1fr; }
            .admin-sidebar { background: linear-gradient(160deg, #0b1a32, #122c56 58%, #173e79); color: #fff; padding: 1.5rem; }
            .admin-brand-title { font-size: 1.25rem; font-weight: 800; }
            .admin-brand-subtitle { font-size: .75rem; margin-top: .25rem; text-transform: uppercase; letter-spacing: .12em; color: #c7dbff; }
            .admin-nav-link { display: flex; margin-top: .35rem; border-radius: .75rem; padding: .625rem .75rem; color: #e8f1ff; text-decoration: none; font-weight: 600; }
            .admin-nav-link.is-active, .admin-nav-link:hover { background: rgba(255,255,255,.12); color: #fff; }
            .admin-main { padding: 1rem; }
            .admin-topbar, .admin-card { background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: 1rem; }
            .admin-topbar { margin-bottom: 1rem; padding: 1rem; }
            .admin-card { padding: 1rem; }
            .admin-headline { margin: 0; font-size: 1.25rem; font-weight: 800; }
            .admin-subline { margin-top: .35rem; font-size: .9rem; color: var(--admin-muted); }
            .admin-btn-soft, .admin-btn-primary {
                display: inline-flex; align-items: center; justify-content: center;
                border-radius: .75rem; padding: .5rem .875rem; font-weight: 700; text-decoration: none;
            }
            .admin-btn-soft { border: 1px solid var(--admin-border); color: #264161; background: #fff; }
            .admin-btn-primary { color: #fff; background: linear-gradient(92deg, #0f62fe, #2f78ff 45%, #ff7a18); }
            .admin-mobile-nav { display: none; }

            @media (max-width: 1024px) {
                .admin-grid { grid-template-columns: 1fr; }
                .admin-sidebar { display: none; }
                .admin-mobile-nav { display: block; }
            }
        </style>
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
