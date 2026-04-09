{{-- Admin Mobile Bottom Nav (oculto no login/register) --}}
@if(! request()->routeIs('filament.admin.auth.*'))
<nav class="admin-bottom-nav">
    <div>
        @php
            $navItems = [
                ['url' => '/admin',                  'icon' => '🏠', 'label' => 'Dashboard',  'match' => 'admin$|admin/dashboard'],
                ['url' => '/admin/orders',           'icon' => '🛒', 'label' => 'Pedidos',    'match' => 'admin/orders'],
                ['url' => '/admin/vehicles',         'icon' => '🚗', 'label' => 'Estoque',    'match' => 'admin/vehicles'],
                ['url' => '/admin/whatsapp-inbox',   'icon' => '💬', 'label' => 'WhatsApp',   'match' => 'admin/whatsapp'],
                ['url' => '/admin/gestao-financeira','icon' => '💰', 'label' => 'Financeiro', 'match' => 'admin/gestao-financeira'],
            ];
        @endphp
        @foreach($navItems as $item)
            <a href="{{ $item['url'] }}"
                class="{{ preg_match('#' . $item['match'] . '#', request()->path()) ? 'active' : 'inactive' }}">
                <span class="nav-icon">{{ $item['icon'] }}</span>
                <span class="nav-label">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
<div class="safe-bottom"></div>
@endif
