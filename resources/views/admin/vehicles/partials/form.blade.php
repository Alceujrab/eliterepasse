{{-- Formulário compartilhado entre create e edit --}}
@php
    $isEdit = isset($vehicle);
    $v = $isEdit ? $vehicle : null;
    $location = $v ? ($v->location ?? []) : [];
    $accessories = $v ? (is_array($v->accessories) ? implode(', ', $v->accessories) : '') : '';
    $media = $v ? ($v->media ?? []) : [];
@endphp

<div class="grid gap-6 lg:grid-cols-3">
    {{-- Coluna principal (2/3) --}}
    <div class="lg:col-span-2 admin-stack">

        {{-- Identificação --}}
        <section class="admin-card">
            <h2 class="admin-section-title">Identificação do veículo</h2>
            <p class="admin-section-note">Dados obrigatórios de marca, modelo, placa e ano.</p>
            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="brand" class="admin-field-label">Marca *</label>
                    <input id="brand" name="brand" type="text" value="{{ old('brand', $v?->brand) }}" required maxlength="60" class="admin-input" placeholder="Ex: Toyota">
                </div>
                <div>
                    <label for="model" class="admin-field-label">Modelo *</label>
                    <input id="model" name="model" type="text" value="{{ old('model', $v?->model) }}" required maxlength="80" class="admin-input" placeholder="Ex: Corolla Cross">
                </div>
                <div class="sm:col-span-2">
                    <label for="version" class="admin-field-label">Versão *</label>
                    <input id="version" name="version" type="text" value="{{ old('version', $v?->version) }}" required maxlength="120" class="admin-input" placeholder="Ex: 2.0 XRE Hybrid CVT">
                </div>
                <div>
                    <label for="plate" class="admin-field-label">Placa *</label>
                    <input id="plate" name="plate" type="text" value="{{ old('plate', $v?->plate) }}" required maxlength="8" class="admin-input font-mono uppercase" placeholder="ABC-1D23">
                </div>
                <div>
                    <label for="renavam" class="admin-field-label">Renavam</label>
                    <input id="renavam" name="renavam" type="text" value="{{ old('renavam', $v?->renavam) }}" maxlength="11" class="admin-input font-mono" placeholder="00000000000">
                </div>
                <div class="sm:col-span-2">
                    <label for="chassi" class="admin-field-label">Chassi</label>
                    <input id="chassi" name="chassi" type="text" value="{{ old('chassi', $v?->chassi) }}" maxlength="17" class="admin-input font-mono uppercase" placeholder="9BWZZZ377VT004251">
                </div>
                <div>
                    <label for="category" class="admin-field-label">Carroceria</label>
                    <select id="category" name="category" class="admin-select">
                        <option value="">Selecione</option>
                        @foreach($categoryOptions as $key => $label)
                            <option value="{{ $key }}" @selected(old('category', $v?->category) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="manufacture_year" class="admin-field-label">Ano fabricação *</label>
                    <input id="manufacture_year" name="manufacture_year" type="number" value="{{ old('manufacture_year', $v?->manufacture_year) }}" required min="1990" max="{{ now()->year + 1 }}" class="admin-input">
                </div>
                <div>
                    <label for="model_year" class="admin-field-label">Ano modelo *</label>
                    <input id="model_year" name="model_year" type="number" value="{{ old('model_year', $v?->model_year) }}" required min="1990" max="{{ now()->year + 2 }}" class="admin-input">
                </div>
                <div>
                    <label for="fipe_code" class="admin-field-label">Código FIPE</label>
                    <input id="fipe_code" name="fipe_code" type="text" value="{{ old('fipe_code', $v?->fipe_code) }}" maxlength="10" class="admin-input font-mono" placeholder="001234-5">
                </div>
                <div>
                    <label for="color" class="admin-field-label">Cor</label>
                    <input id="color" name="color" type="text" value="{{ old('color', $v?->color) }}" maxlength="40" class="admin-input" placeholder="Ex: Prata Metálico">
                </div>
            </div>
        </section>

        {{-- Especificações técnicas --}}
        <section class="admin-card">
            <h2 class="admin-section-title">Especificações técnicas</h2>
            <p class="admin-section-note">Motor, combustível, câmbio, quilometragem, direção e portas.</p>
            <div class="mt-5 grid gap-4 sm:grid-cols-3">
                <div>
                    <label for="mileage" class="admin-field-label">Quilometragem (km) *</label>
                    <input id="mileage" name="mileage" type="number" value="{{ old('mileage', $v?->mileage ?? 0) }}" required min="0" class="admin-input">
                </div>
                <div>
                    <label for="num_owners" class="admin-field-label">Nº de donos</label>
                    <input id="num_owners" name="num_owners" type="number" value="{{ old('num_owners', $v?->num_owners) }}" min="0" max="20" class="admin-input" placeholder="Ex: 1">
                </div>
                <div>
                    <label for="fuel_type" class="admin-field-label">Combustível</label>
                    <select id="fuel_type" name="fuel_type" class="admin-select">
                        <option value="">Selecione</option>
                        @foreach($fuelOptions as $key => $label)
                            <option value="{{ $key }}" @selected(old('fuel_type', $v?->fuel_type) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="transmission" class="admin-field-label">Câmbio</label>
                    <select id="transmission" name="transmission" class="admin-select">
                        <option value="">Selecione</option>
                        @foreach($transmissionOptions as $key => $label)
                            <option value="{{ $key }}" @selected(old('transmission', $v?->transmission) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="steering" class="admin-field-label">Direção</label>
                    <select id="steering" name="steering" class="admin-select">
                        <option value="">Selecione</option>
                        @foreach($steeringOptions as $key => $label)
                            <option value="{{ $key }}" @selected(old('steering', $v?->steering) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="engine" class="admin-field-label">Motor</label>
                    <input id="engine" name="engine" type="text" value="{{ old('engine', $v?->engine) }}" maxlength="60" class="admin-input" placeholder="Ex: 2.0 TwinPower Turbo">
                </div>
                <div>
                    <label for="doors" class="admin-field-label">Portas</label>
                    <input id="doors" name="doors" type="number" value="{{ old('doors', $v?->doors ?? 4) }}" min="2" max="5" class="admin-input">
                </div>
            </div>
        </section>

        {{-- Descrição / Observações --}}
        <section class="admin-card">
            <h2 class="admin-section-title">Descrição do anúncio</h2>
            <p class="admin-section-note">Texto livre para descrever o veículo, histórico, diferenciais e observações internas.</p>
            <div class="mt-4">
                <label for="description" class="admin-field-label">Descrição</label>
                <textarea id="description" name="description" rows="5" maxlength="2000" class="admin-input" placeholder="Descreva o estado geral do veículo, diferenciais, histórico de manutenção, etc.">{{ old('description', $v?->description) }}</textarea>
                <p class="mt-1 text-xs text-slate-400">Máximo 2.000 caracteres.</p>
            </div>
        </section>

        {{-- Fotos --}}
        <section class="admin-card">
            <h2 class="admin-section-title">Fotos do veículo</h2>
            <p class="admin-section-note">A primeira imagem será a capa do anúncio. Máx. 5 MB por foto.</p>

            @if(! empty($media))
                <div class="mt-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                    @foreach($media as $path)
                        <label class="relative group cursor-pointer rounded-xl overflow-hidden border border-slate-200 aspect-square">
                            <img src="{{ asset('storage/' . $path) }}" alt="Foto" class="w-full h-full object-cover">
                            <input type="checkbox" name="remove_photos[]" value="{{ $path }}" class="absolute top-2 right-2 h-5 w-5 accent-rose-500">
                            <span class="absolute inset-0 bg-rose-500/0 group-has-[:checked]:bg-rose-500/30 transition"></span>
                        </label>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-slate-500">Marque as fotos que deseja remover.</p>
            @endif

            <div class="mt-4">
                <label for="photos" class="admin-field-label">Adicionar novas fotos</label>
                <input id="photos" name="photos[]" type="file" multiple accept="image/jpeg,image/png,image/webp" class="admin-input">
            </div>
        </section>

        {{-- Vídeo --}}
        <section class="admin-card">
            <h2 class="admin-section-title">Vídeo</h2>
            <p class="admin-section-note">Link do YouTube ou outro serviço de vídeo para walkthrough do veículo.</p>
            <div class="mt-4">
                <label for="video_url" class="admin-field-label">URL do vídeo</label>
                <input id="video_url" name="video_url" type="url" value="{{ old('video_url', $v?->video_url) }}" maxlength="255" class="admin-input" placeholder="https://www.youtube.com/watch?v=...">
            </div>
        </section>

        {{-- Acessórios --}}
        <section class="admin-card">
            <h2 class="admin-section-title">Acessórios</h2>
            <p class="admin-section-note">Separe por vírgula. Ex: Ar condicionado, Direção elétrica, Airbag</p>
            <div class="mt-4">
                <label for="accessories" class="admin-field-label">Lista de acessórios</label>
                <textarea id="accessories" name="accessories" rows="3" class="admin-input" placeholder="Ar condicionado, Direção elétrica, Central multimídia, Câmera de ré...">{{ old('accessories', $accessories) }}</textarea>
            </div>
        </section>
    </div>

    {{-- Coluna lateral (1/3) --}}
    <div class="admin-stack">

        {{-- Precificação --}}
        <section class="admin-card">
            <h2 class="admin-section-title">Precificação</h2>
            <div class="mt-4 admin-stack">
                <div>
                    <label for="fipe_price" class="admin-field-label">Tabela FIPE (R$)</label>
                    <input id="fipe_price" name="fipe_price" type="number" step="0.01" min="0" value="{{ old('fipe_price', $v?->fipe_price) }}" class="admin-input" placeholder="0,00">
                </div>
                <div>
                    <label for="sale_price" class="admin-field-label">Preço de venda (R$)</label>
                    <input id="sale_price" name="sale_price" type="number" step="0.01" min="0" value="{{ old('sale_price', $v?->sale_price) }}" class="admin-input" placeholder="0,00">
                </div>
                <div>
                    <label for="profit_margin" class="admin-field-label">Margem (%)</label>
                    <input id="profit_margin" name="profit_margin" type="number" step="0.1" value="{{ old('profit_margin', $v?->profit_margin) }}" class="admin-input" placeholder="0,0">
                </div>
            </div>
        </section>

        {{-- Localização --}}
        <section class="admin-card">
            <h2 class="admin-section-title">Localização</h2>
            <div class="mt-4 admin-stack">
                <div>
                    <label for="location_name" class="admin-field-label">Pátio / Loja</label>
                    <input id="location_name" name="location_name" type="text" value="{{ old('location_name', $location['name'] ?? '') }}" maxlength="80" class="admin-input" placeholder="Ex: Pátio Contagem">
                </div>
                <div>
                    <label for="location_city" class="admin-field-label">Cidade</label>
                    <input id="location_city" name="location_city" type="text" value="{{ old('location_city', $location['city'] ?? '') }}" maxlength="80" class="admin-input" placeholder="Ex: Contagem">
                </div>
                <div>
                    <label for="location_state" class="admin-field-label">UF</label>
                    <select id="location_state" name="location_state" class="admin-select">
                        <option value="">Selecione</option>
                        @foreach($ufOptions as $uf)
                            <option value="{{ $uf }}" @selected(old('location_state', $location['state'] ?? '') === $uf)>{{ $uf }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        {{-- Status e destaques --}}
        <section class="admin-card">
            <h2 class="admin-section-title">Status e destaques</h2>
            <div class="mt-4 admin-stack">
                <div>
                    <label for="status" class="admin-field-label">Status *</label>
                    <select id="status" name="status" required class="admin-select">
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}" @selected(old('status', $v?->status ?? 'available') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-3 pt-2">
                    <label class="flex items-center gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="is_on_sale" value="1" @checked(old('is_on_sale', $v?->is_on_sale)) class="h-5 w-5 rounded border-slate-300 accent-orange-500">
                        Em oferta
                    </label>
                    <label class="flex items-center gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="is_just_arrived" value="1" @checked(old('is_just_arrived', $v?->is_just_arrived)) class="h-5 w-5 rounded border-slate-300 accent-orange-500">
                        Recém chegado
                    </label>
                    <label class="flex items-center gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="has_report" value="1" @checked(old('has_report', $v?->has_report)) class="h-5 w-5 rounded border-slate-300 accent-orange-500">
                        Com laudo
                    </label>
                    <label class="flex items-center gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="has_factory_warranty" value="1" @checked(old('has_factory_warranty', $v?->has_factory_warranty)) class="h-5 w-5 rounded border-slate-300 accent-orange-500">
                        Garantia de fábrica
                    </label>
                    <label class="flex items-center gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="accepts_trade" value="1" @checked(old('accepts_trade', $v?->accepts_trade)) class="h-5 w-5 rounded border-slate-300 accent-orange-500">
                        Aceita troca
                    </label>
                    <label class="flex items-center gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="ipva_paid" value="1" @checked(old('ipva_paid', $v?->ipva_paid)) class="h-5 w-5 rounded border-slate-300 accent-orange-500">
                        IPVA pago
                    </label>
                    <label class="flex items-center gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="licensing_ok" value="1" @checked(old('licensing_ok', $v?->licensing_ok)) class="h-5 w-5 rounded border-slate-300 accent-orange-500">
                        Licenciamento em dia
                    </label>
                    <label class="flex items-center gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="is_armored" value="1" @checked(old('is_armored', $v?->is_armored)) class="h-5 w-5 rounded border-slate-300 accent-orange-500">
                        Blindado
                    </label>
                </div>
            </div>
        </section>

        {{-- Botões --}}
        <div class="flex flex-col gap-3">
            <button type="submit" class="admin-btn-primary w-full justify-center py-3">
                {{ $isEdit ? 'Salvar alterações' : 'Cadastrar veículo' }}
            </button>
            <a href="{{ $isEdit ? route('admin.v2.vehicles.show', $vehicle) : route('admin.v2.vehicles.index') }}" class="admin-btn-soft w-full justify-center py-3">
                Cancelar
            </a>
        </div>
    </div>
</div>
