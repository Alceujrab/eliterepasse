<div class="flex flex-wrap gap-2">
    @if($client->status !== 'ativo')
        <form method="POST" action="{{ route('admin.v2.clients.approve', $client) }}">
            @csrf
            <button type="submit" class="admin-btn-primary">Aprovar</button>
        </form>
    @endif

    @if($client->status === 'ativo')
        <form method="POST" action="{{ route('admin.v2.clients.block', $client) }}">
            @csrf
            <button type="submit" class="admin-btn-soft">Bloquear</button>
        </form>
    @endif

    <a href="{{ route('admin.v2.clients.show', $client) }}" class="admin-btn-soft">Abrir v2</a>
    <a href="/admin/clients/{{ $client->id }}" class="admin-btn-soft">Legado</a>
</div>