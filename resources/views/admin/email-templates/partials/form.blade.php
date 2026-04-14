<form method="POST" action="{{ $action }}" class="mt-5 admin-stack">
    @csrf

    <div class="grid gap-4 xl:grid-cols-2">
        <div class="admin-info-card">
            <label class="admin-detail-label">Nome</label>
            <input type="text" name="nome" value="{{ old('nome', $template?->nome) }}" {{ $template?->isSystemTemplate() ? 'readonly' : '' }}
                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400 {{ $template?->isSystemTemplate() ? 'bg-slate-50 text-slate-500' : '' }}">
        </div>

        <div class="admin-info-card">
            <label class="admin-detail-label">Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $template?->slug) }}" {{ $template?->isSystemTemplate() ? 'readonly' : '' }}
                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400 {{ $template?->isSystemTemplate() ? 'bg-slate-50 text-slate-500' : '' }}">
        </div>

        <div class="admin-info-card xl:col-span-2">
            <label class="admin-detail-label">Assunto</label>
            <input type="text" name="assunto" value="{{ old('assunto', $template?->assunto) }}"
                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
        </div>

        <div class="admin-info-card">
            <label class="admin-detail-label">Saudacao</label>
            <input type="text" name="saudacao" value="{{ old('saudacao', $template?->saudacao) }}"
                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
        </div>

        <div class="admin-info-card">
            <label class="admin-detail-label">Template ativo</label>
            <label class="mt-3 inline-flex items-center gap-3 text-sm font-semibold text-slate-600">
                <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $template?->ativo ?? true)) class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                Usar este template nas notificacoes
            </label>
        </div>

        <div class="admin-info-card xl:col-span-2">
            <label class="admin-detail-label">Corpo</label>
            <textarea name="corpo" rows="10" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">{{ old('corpo', $template?->corpo) }}</textarea>
        </div>

        <div class="admin-info-card">
            <label class="admin-detail-label">Texto do botao</label>
            <input type="text" name="texto_acao" value="{{ old('texto_acao', $template?->texto_acao) }}"
                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
        </div>

        <div class="admin-info-card">
            <label class="admin-detail-label">URL do botao</label>
            <input type="text" name="url_acao" value="{{ old('url_acao', $template?->url_acao) }}"
                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">
        </div>

        <div class="admin-info-card xl:col-span-2">
            <label class="admin-detail-label">Texto de rodape</label>
            <textarea name="texto_rodape" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400">{{ old('texto_rodape', $template?->texto_rodape) }}</textarea>
        </div>

        <div class="admin-info-card xl:col-span-2">
            <label class="admin-detail-label">Variaveis disponiveis</label>
            <div class="mt-3 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @php($variables = old('variaveis_disponiveis', $template?->variaveis_disponiveis ?? ['nome']))
                @for($index = 0; $index < max(6, count($variables)); $index++)
                    <input type="text" name="variaveis_disponiveis[]" value="{{ $variables[$index] ?? '' }}"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium outline-none transition focus:border-blue-400"
                        placeholder="nome_da_variavel">
                @endfor
            </div>
            @if($template)
                <div class="mt-4 flex flex-wrap gap-2">
                    @forelse($sampleVariables as $key => $value)
                        <span class="admin-filter-chip is-active"><span>{{ '{' . '{' . $key . '}' . '}' }}</span></span>
                    @empty
                        <span class="admin-row-meta">Sem variaveis cadastradas.</span>
                    @endforelse
                </div>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <button type="submit" class="admin-btn-primary">Salvar template</button>
        @if($template)
            <a href="{{ route('admin.v2.email-templates.index') }}" class="admin-btn-soft">Voltar para lista</a>
        @endif
    </div>
</form>