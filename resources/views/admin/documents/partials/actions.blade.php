<div class="admin-action-cluster">
    <a href="{{ $document->url }}" target="_blank" rel="noreferrer" class="admin-btn-soft">Visualizar</a>

    @if($document->status === 'pendente')
        <form method="POST" action="{{ route('admin.v2.documents.verify', $document) }}">
            @csrf
            <button type="submit" class="admin-btn-primary">Verificar</button>
        </form>
    @endif

    <a href="/admin/documents/{{ $document->id }}" class="admin-btn-soft">Ver legado</a>
</div>

@if(in_array($document->status, ['pendente', 'verificado'], true))
    <details class="admin-inline-review">
        <summary>Rejeitar com motivo</summary>
        <form method="POST" action="{{ route('admin.v2.documents.reject', $document) }}">
            @csrf
            <textarea name="motivo_rejeicao" class="admin-textarea" placeholder="Explique para o cliente o que precisa ser corrigido ou reenviado." required></textarea>
            <div class="mt-3 flex flex-wrap gap-2">
                <button type="submit" class="admin-btn-danger">Confirmar rejeicao</button>
            </div>
        </form>
    </details>
@elseif($document->motivo_rejeicao)
    <div class="admin-inline-review">
        <div class="admin-row-meta">Motivo da rejeicao: {{ $document->motivo_rejeicao }}</div>
    </div>
@endif