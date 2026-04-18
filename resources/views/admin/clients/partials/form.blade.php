{{-- Formulário compartilhado entre create e edit --}}
@php
    $isEdit = isset($client);
    $c = $isEdit ? $client : null;
@endphp

<div class="grid gap-6 lg:grid-cols-3">
    {{-- Coluna principal (2/3) --}}
    <div class="lg:col-span-2 admin-stack">

        {{-- Dados da empresa --}}
        <section class="admin-card">
            <h2 class="admin-section-title">Dados da empresa (PJ)</h2>
            <p class="admin-section-note">Razao social, CNPJ e dados do responsavel.</p>
            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="razao_social" class="admin-field-label">Razão Social *</label>
                    <input id="razao_social" name="razao_social" type="text" value="{{ old('razao_social', $c?->razao_social) }}" required maxlength="255" class="admin-input">
                </div>
                <div>
                    <label for="nome_fantasia" class="admin-field-label">Nome Fantasia</label>
                    <input id="nome_fantasia" name="nome_fantasia" type="text" value="{{ old('nome_fantasia', $c?->nome_fantasia) }}" maxlength="255" class="admin-input">
                </div>
                <div>
                    <label for="cnpj" class="admin-field-label">CNPJ</label>
                    <input id="cnpj" name="cnpj" type="text" value="{{ old('cnpj', $c?->cnpj) }}" maxlength="20" class="admin-input font-mono" placeholder="00.000.000/0000-00">
                </div>
                <div>
                    <label for="inscricao_estadual" class="admin-field-label">Inscrição Estadual</label>
                    <input id="inscricao_estadual" name="inscricao_estadual" type="text" value="{{ old('inscricao_estadual', $c?->inscricao_estadual) }}" maxlength="20" class="admin-input">
                </div>
                <div>
                    <label for="name" class="admin-field-label">Responsável (Nome) *</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $c?->name) }}" required maxlength="255" class="admin-input">
                </div>
                <div>
                    <label for="email" class="admin-field-label">E-mail *</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $c?->email) }}" required maxlength="255" class="admin-input">
                </div>
                <div>
                    <label for="phone" class="admin-field-label">WhatsApp / Telefone</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone', $c?->phone) }}" maxlength="20" class="admin-input" placeholder="(31) 9 9999-9999">
                </div>
                <div>
                    <label for="cpf" class="admin-field-label">CPF do Responsável</label>
                    <input id="cpf" name="cpf" type="text" value="{{ old('cpf', $c?->cpf) }}" maxlength="14" class="admin-input font-mono" placeholder="000.000.000-00">
                </div>
            </div>
        </section>

        {{-- Endereço --}}
        <section class="admin-card">
            <h2 class="admin-section-title">Endereço</h2>
            <p class="admin-section-note">Endereco completo para correspondencia e contratos.</p>
            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="cep" class="admin-field-label">CEP</label>
                    <input id="cep" name="cep" type="text" value="{{ old('cep', $c?->cep) }}" maxlength="10" class="admin-input font-mono" placeholder="00000-000">
                </div>
                <div>
                    <label for="logradouro" class="admin-field-label">Rua / Avenida</label>
                    <input id="logradouro" name="logradouro" type="text" value="{{ old('logradouro', $c?->logradouro) }}" maxlength="255" class="admin-input">
                </div>
                <div>
                    <label for="numero" class="admin-field-label">Número</label>
                    <input id="numero" name="numero" type="text" value="{{ old('numero', $c?->numero) }}" maxlength="20" class="admin-input">
                </div>
                <div>
                    <label for="complemento" class="admin-field-label">Complemento</label>
                    <input id="complemento" name="complemento" type="text" value="{{ old('complemento', $c?->complemento) }}" maxlength="255" class="admin-input">
                </div>
                <div>
                    <label for="bairro" class="admin-field-label">Bairro</label>
                    <input id="bairro" name="bairro" type="text" value="{{ old('bairro', $c?->bairro) }}" maxlength="100" class="admin-input">
                </div>
                <div>
                    <label for="cidade" class="admin-field-label">Cidade</label>
                    <input id="cidade" name="cidade" type="text" value="{{ old('cidade', $c?->cidade) }}" maxlength="100" class="admin-input">
                </div>
                <div>
                    <label for="estado" class="admin-field-label">Estado (UF)</label>
                    <select id="estado" name="estado" class="admin-select">
                        <option value="">Selecione</option>
                        @foreach($estadoOptions as $uf => $nome)
                            <option value="{{ $uf }}" @selected(old('estado', $c?->estado) === $uf)>{{ $uf }} - {{ $nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        {{-- Observações --}}
        <section class="admin-card">
            <h2 class="admin-section-title">Observações internas</h2>
            <p class="admin-section-note">Notas internas visiveis apenas para o time administrativo.</p>
            <div class="mt-4">
                <textarea name="observacoes" rows="4" maxlength="2000" class="admin-input" placeholder="Observações sobre o cliente...">{{ old('observacoes', $c?->observacoes) }}</textarea>
            </div>
        </section>
    </div>

    {{-- Coluna lateral (1/3) --}}
    <div class="admin-stack">

        {{-- Status e acesso --}}
        <section class="admin-card">
            <h2 class="admin-section-title">Status e acesso</h2>
            <div class="mt-4 admin-stack">
                <div>
                    <label for="status" class="admin-field-label">Status da conta *</label>
                    <select id="status" name="status" required class="admin-select">
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}" @selected(old('status', $c?->status ?? 'pendente') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="password" class="admin-field-label">{{ $isEdit ? 'Nova senha (opcional)' : 'Senha *' }}</label>
                    <input id="password" name="password" type="password" minlength="6" class="admin-input" {{ $isEdit ? '' : 'required' }} placeholder="{{ $isEdit ? 'Deixe em branco para manter' : 'Min. 6 caracteres' }}">
                </div>
            </div>

            @if($isEdit && $c->aprovado_em)
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                    Aprovado em {{ $c->aprovado_em->format('d/m/Y H:i') }}
                    @if($c->approvedBy)
                        por {{ $c->approvedBy->name }}
                    @endif
                </div>
            @endif
        </section>

        {{-- Botões --}}
        <div class="flex flex-col gap-3">
            <button type="submit" class="admin-btn-primary w-full justify-center py-3">
                {{ $isEdit ? 'Salvar alterações' : 'Cadastrar cliente' }}
            </button>
            <a href="{{ $isEdit ? route('admin.v2.clients.show', $client) : route('admin.v2.clients.index') }}" class="admin-btn-soft w-full justify-center py-3">
                Cancelar
            </a>
        </div>
    </div>
</div>
