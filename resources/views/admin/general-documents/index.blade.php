@extends('admin.layouts.app')

@php
    $pageTitle = 'Documentos Gerais';
    $pageSubtitle = 'Documentos internos disponibilizados para download no portal do lojista.';
@endphp

@section('content')
    @if(session('admin_success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('admin_success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- KPIs --}}
    <section class="admin-metrics-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Total</p>
            <p class="admin-metric-value">{{ number_format($summary['total']) }}</p>
            <p class="admin-metric-footnote">Documentos cadastrados</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Ativos</p>
            <p class="admin-metric-value">{{ number_format($summary['ativos']) }}</p>
            <p class="admin-metric-footnote">Visíveis no portal</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Inativos</p>
            <p class="admin-metric-value">{{ number_format($summary['inativos']) }}</p>
            <p class="admin-metric-footnote">Ocultos do portal</p>
        </article>
    </section>

    <section class="mt-6 admin-split-grid">
        <div class="admin-stack">
            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <span class="admin-tag admin-tag-new">documentos gerais</span>
                        <h2 class="mt-3 admin-section-title">Biblioteca de documentos</h2>
                        <p class="admin-section-note">Gerencie documentos internos como manuais, tabelas de preço, políticas, etc.</p>
                    </div>
                    <div class="admin-toolbar-actions">
                        <a href="{{ route('admin.v2.general-documents.index') }}" class="admin-btn-soft">Atualizar</a>
                    </div>
                </div>

                {{-- Filtros --}}
                <form method="GET" action="{{ route('admin.v2.general-documents.index') }}" class="mt-4 flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Buscar</label>
                        <input type="text" name="q" value="{{ $search }}" placeholder="Título ou descrição..."
                            class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                        <select name="status" class="rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="ativo" @selected($status === 'ativo')>Ativos</option>
                            <option value="inativo" @selected($status === 'inativo')>Inativos</option>
                        </select>
                    </div>
                    <button type="submit" class="admin-btn-primary">Filtrar</button>
                    @if($hasActiveFilters)
                        <a href="{{ route('admin.v2.general-documents.index') }}" class="admin-btn-soft">Limpar</a>
                    @endif
                </form>

                {{-- Tabela desktop --}}
                <div class="mt-5 hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left py-3 px-3 text-xs font-bold text-gray-400 uppercase">Título</th>
                                <th class="text-left py-3 px-3 text-xs font-bold text-gray-400 uppercase">Descrição</th>
                                <th class="text-center py-3 px-3 text-xs font-bold text-gray-400 uppercase">Status</th>
                                <th class="text-center py-3 px-3 text-xs font-bold text-gray-400 uppercase">Arquivo</th>
                                <th class="text-left py-3 px-3 text-xs font-bold text-gray-400 uppercase">Criado em</th>
                                <th class="text-right py-3 px-3 text-xs font-bold text-gray-400 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($documents as $doc)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-3 px-3 font-semibold text-gray-800">{{ $doc->title }}</td>
                                    <td class="py-3 px-3 text-gray-500 max-w-[200px] truncate">{{ $doc->description ?? '—' }}</td>
                                    <td class="py-3 px-3 text-center">
                                        @if($doc->is_active)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">✅ Ativo</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">⏸ Inativo</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold text-xs underline">
                                            📎 Abrir
                                        </a>
                                    </td>
                                    <td class="py-3 px-3 text-gray-400 text-xs">{{ $doc->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="py-3 px-3 text-right">
                                        <div x-data="{ open: false }" class="inline-block relative">
                                            <button @click="open = !open" class="admin-btn-soft text-xs px-2 py-1">⋯</button>
                                            <div x-show="open" @click.away="open = false" x-cloak
                                                class="absolute right-0 mt-1 w-48 bg-white border border-gray-200 rounded-xl shadow-lg z-50 py-1">
                                                <button @click="open = false; $dispatch('edit-doc', { id: {{ $doc->id }}, title: '{{ addslashes($doc->title) }}', description: '{{ addslashes($doc->description ?? '') }}', is_active: {{ $doc->is_active ? 'true' : 'false' }} })"
                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 font-semibold">✏️ Editar</button>
                                                <form method="POST" action="{{ route('admin.v2.general-documents.destroy', $doc) }}" onsubmit="return confirm('Tem certeza que deseja remover este documento?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-red-50 text-red-600 font-semibold">🗑 Remover</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400">
                                        <div class="text-4xl mb-2">📂</div>
                                        <p class="font-semibold">Nenhum documento geral cadastrado.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Lista mobile --}}
                <div class="mt-5 md:hidden space-y-3">
                    @forelse($documents as $doc)
                        <div class="admin-card p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-800 truncate">{{ $doc->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $doc->description ?? '—' }}</p>
                                </div>
                                @if($doc->is_active)
                                    <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Ativo</span>
                                @else
                                    <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">Inativo</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 mt-3">
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="admin-btn-soft text-xs">📎 Abrir</a>
                                <span class="text-xs text-gray-400">{{ $doc->created_at?->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <div class="text-4xl mb-2">📂</div>
                            <p class="font-semibold">Nenhum documento geral.</p>
                        </div>
                    @endforelse
                </div>

                @if($documents->hasPages())
                    <div class="mt-5">{{ $documents->links() }}</div>
                @endif
            </section>
        </div>

        {{-- Sidebar: Formulário de cadastro --}}
        <aside class="admin-stack" x-data="{
            editing: false, editId: null, editTitle: '', editDescription: '', editActive: true,
            init() {
                window.addEventListener('edit-doc', (e) => {
                    this.editing = true;
                    this.editId = e.detail.id;
                    this.editTitle = e.detail.title;
                    this.editDescription = e.detail.description;
                    this.editActive = e.detail.is_active;
                    this.$nextTick(() => this.$refs.editTitle?.focus());
                });
            },
            cancelEdit() { this.editing = false; this.editId = null; }
        }">
            {{-- Formulário novo --}}
            <section class="admin-card" x-show="!editing">
                <h3 class="admin-section-title text-base">📄 Novo documento geral</h3>
                <p class="admin-section-note mt-1">Cadastre documentos internos visíveis no portal.</p>

                <form method="POST" action="{{ route('admin.v2.general-documents.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título *</label>
                        <input type="text" name="title" required placeholder="Ex: Tabela de Preços 2026"
                            class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Descrição</label>
                        <textarea name="description" rows="2" placeholder="Breve descrição do documento..."
                            class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Arquivo *</label>
                        <input type="file" name="arquivo" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                            class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" checked id="new_is_active" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="new_is_active" class="text-sm font-semibold text-gray-600">Ativo (visível no portal)</label>
                    </div>
                    <button type="submit" class="w-full admin-btn-primary py-2.5">Cadastrar documento</button>
                </form>
            </section>

            {{-- Formulário edição --}}
            <section class="admin-card" x-show="editing" x-cloak>
                <div class="flex items-center justify-between">
                    <h3 class="admin-section-title text-base">✏️ Editar documento</h3>
                    <button @click="cancelEdit()" class="text-xs text-gray-400 hover:text-gray-600 font-bold">✕ Cancelar</button>
                </div>

                <form method="POST" :action="'/painel-admin/documentos-gerais/' + editId" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título *</label>
                        <input x-ref="editTitle" type="text" name="title" x-model="editTitle" required
                            class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Descrição</label>
                        <textarea name="description" rows="2" x-model="editDescription"
                            class="w-full rounded-xl border border-gray-200 bg-[#f8fafc] px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Substituir arquivo (opcional)</label>
                        <input type="file" name="arquivo" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                            class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" :checked="editActive" id="edit_is_active" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="edit_is_active" class="text-sm font-semibold text-gray-600">Ativo</label>
                    </div>
                    <button type="submit" class="w-full admin-btn-primary py-2.5">Salvar alterações</button>
                </form>
            </section>
        </aside>
    </section>
@endsection
