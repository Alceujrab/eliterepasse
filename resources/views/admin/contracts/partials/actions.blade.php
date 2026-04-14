<div class="flex flex-wrap gap-2">
    @if(in_array($contract->status, ['rascunho', 'aguardando'], true))
        <form method="POST" action="{{ route('admin.v2.contracts.send-to-sign', $contract) }}">
            @csrf
            <button type="submit" class="admin-btn-primary">Enviar p/ assinar</button>
        </form>
    @endif

    @if($contract->assinaturaComprador)
        <form method="POST" action="{{ route('admin.v2.contracts.copy-link', $contract) }}">
            @csrf
            <button type="submit" class="admin-btn-soft">Copiar link</button>
        </form>
    @endif

    <a href="{{ route('admin.v2.contracts.show', $contract) }}" class="admin-btn-soft">Abrir v2</a>
</div>