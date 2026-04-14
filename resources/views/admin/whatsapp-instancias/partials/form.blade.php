<form method="POST" action="{{ $action }}" class="mt-5 admin-stack">
    @csrf

    <div class="grid gap-4 xl:grid-cols-2">
        <div class="admin-info-card">
            <label class="admin-detail-label">Nome</label>
            <input type="text" name="nome" value="{{ old('nome', $instance?->nome) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
        </div>
        <div class="admin-info-card">
            <label class="admin-detail-label">Evolution ID</label>
            <input type="text" name="instancia" value="{{ old('instancia', $instance?->instancia) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
        </div>
        <div class="admin-info-card xl:col-span-2">
            <label class="admin-detail-label">URL base</label>
            <input type="text" name="url_base" value="{{ old('url_base', $instance?->url_base) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400" placeholder="https://api.auto.inf.br">
        </div>
        <div class="admin-info-card xl:col-span-2">
            <label class="admin-detail-label">API Key</label>
            <textarea name="api_key" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">{{ old('api_key', $instance?->api_key) }}</textarea>
        </div>
        <div class="admin-info-card">
            <label class="admin-detail-label">Ativa</label>
            <label class="mt-3 inline-flex items-center gap-3 text-sm font-semibold text-slate-600">
                <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $instance?->ativo ?? true)) class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                Permitir uso desta instancia
            </label>
        </div>
        <div class="admin-info-card">
            <label class="admin-detail-label">Padrao</label>
            <label class="mt-3 inline-flex items-center gap-3 text-sm font-semibold text-slate-600">
                <input type="checkbox" name="padrao" value="1" @checked(old('padrao', $instance?->padrao ?? false)) class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                Tornar instancia principal
            </label>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <button type="submit" class="admin-btn-primary">Salvar instancia</button>
    </div>
</form>