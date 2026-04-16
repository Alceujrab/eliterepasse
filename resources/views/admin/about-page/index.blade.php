@extends('admin.layouts.app', ['pageTitle' => 'Página Sobre Nós', 'pageSubtitle' => 'Gerencie conteúdo, equipe, depoimentos, galeria e vídeo'])

@section('content')
<div x-data="{ tab: 'hero' }">

    @if(session('admin_success'))
        <div class="admin-card" style="background: #ecfdf5; border-color: #6ee7b7; margin-bottom: 1rem; color: #065f46;">
            ✅ {{ session('admin_success') }}
        </div>
    @endif

    {{-- ABAS --}}
    <div style="display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1rem;">
        @foreach([
            'hero' => '🏠 Hero',
            'mvv' => '🎯 Missão/Visão/Valores',
            'history' => '📖 História',
            'stats' => '📊 Números',
            'team' => '👥 Equipe',
            'testimonials' => '💬 Depoimentos',
            'gallery' => '📷 Galeria',
            'video' => '🎬 Vídeo',
        ] as $key => $label)
            <button @click="tab = '{{ $key }}'"
                    :style="tab === '{{ $key }}' ? 'background: linear-gradient(92deg, #0f62fe, #2f78ff); color: #fff;' : ''"
                    class="admin-btn-soft" style="font-size: .85rem;">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <form method="POST" action="{{ route('admin.v2.about-page.upsert') }}" enctype="multipart/form-data">
        @csrf

        {{-- ═══════════ ABA: HERO ═══════════ --}}
        <div x-show="tab === 'hero'" class="admin-card" style="margin-bottom: 1rem;">
            <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Hero da Página Sobre Nós</h2>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 700; font-size: .85rem;">Título Principal</label>
                <input type="text" name="about_page_hero_title"
                       value="{{ old('about_page_hero_title', $setting->about_page_hero_title ?? $defaults['about_page_hero_title']) }}"
                       style="width: 100%; padding: .6rem .75rem; border: 1px solid #d9e3f1; border-radius: .75rem; margin-top: .35rem;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 700; font-size: .85rem;">Subtítulo</label>
                <textarea name="about_page_hero_subtitle" rows="2"
                          style="width: 100%; padding: .6rem .75rem; border: 1px solid #d9e3f1; border-radius: .75rem; margin-top: .35rem;">{{ old('about_page_hero_subtitle', $setting->about_page_hero_subtitle ?? $defaults['about_page_hero_subtitle']) }}</textarea>
            </div>
        </div>

        {{-- ═══════════ ABA: MISSÃO/VISÃO/VALORES ═══════════ --}}
        <div x-show="tab === 'mvv'" class="admin-card" style="margin-bottom: 1rem;">
            <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Missão, Visão e Valores</h2>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 700; font-size: .85rem;">Missão</label>
                <textarea name="about_page_mission" rows="3"
                          style="width: 100%; padding: .6rem .75rem; border: 1px solid #d9e3f1; border-radius: .75rem; margin-top: .35rem;">{{ old('about_page_mission', $setting->about_page_mission ?? $defaults['about_page_mission']) }}</textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 700; font-size: .85rem;">Visão</label>
                <textarea name="about_page_vision" rows="3"
                          style="width: 100%; padding: .6rem .75rem; border: 1px solid #d9e3f1; border-radius: .75rem; margin-top: .35rem;">{{ old('about_page_vision', $setting->about_page_vision ?? $defaults['about_page_vision']) }}</textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 700; font-size: .85rem;">Valores (um por linha)</label>
                <textarea name="about_page_values" rows="5"
                          style="width: 100%; padding: .6rem .75rem; border: 1px solid #d9e3f1; border-radius: .75rem; margin-top: .35rem;">{{ old('about_page_values', $setting->about_page_values ?? $defaults['about_page_values']) }}</textarea>
                <p style="font-size: .75rem; color: #5f6f86; margin-top: .25rem;">Use uma linha por valor. Ex: Transparência — Operamos com clareza.</p>
            </div>
        </div>

        {{-- ═══════════ ABA: HISTÓRIA ═══════════ --}}
        <div x-show="tab === 'history'" class="admin-card" style="margin-bottom: 1rem;">
            <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Nossa História</h2>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 700; font-size: .85rem;">Texto da história</label>
                <textarea name="about_page_history" rows="6"
                          style="width: 100%; padding: .6rem .75rem; border: 1px solid #d9e3f1; border-radius: .75rem; margin-top: .35rem;">{{ old('about_page_history', $setting->about_page_history ?? $defaults['about_page_history']) }}</textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 700; font-size: .85rem;">Imagem da história</label>
                @if(filled($setting->about_page_history_image))
                    <div style="margin: .5rem 0;">
                        <img src="{{ asset($setting->about_page_history_image) }}" alt="História" style="max-height: 120px; border-radius: .75rem;">
                    </div>
                @endif
                <input type="file" name="about_page_history_image" accept="image/*"
                       style="margin-top: .35rem; font-size: .85rem;">
                <p style="font-size: .75rem; color: #5f6f86; margin-top: .25rem;">Recomendado: 800x500px. Máx 3MB.</p>
            </div>
        </div>

        {{-- ═══════════ ABA: NÚMEROS ═══════════ --}}
        <div x-show="tab === 'stats'" class="admin-card" style="margin-bottom: 1rem;">
            <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Números / Estatísticas</h2>
            <p style="font-size: .8rem; color: #5f6f86; margin-bottom: 1rem;">Até 6 itens. Ex: "500+" → "Veículos Negociados"</p>

            @foreach($statsRows as $i => $stat)
            <div style="display: grid; grid-template-columns: 120px 1fr; gap: .5rem; margin-bottom: .5rem; align-items: center;">
                <div style="display: flex; align-items: center; gap: .35rem;">
                    <span style="font-size: .75rem; color: #5f6f86; font-weight: 700;">#{{ $i + 1 }}</span>
                    <input type="text" name="about_page_stats[{{ $i }}][value]"
                           value="{{ $stat['value'] ?? '' }}" placeholder="500+"
                           style="width: 100%; padding: .5rem; border: 1px solid #d9e3f1; border-radius: .5rem; font-size: .85rem; font-weight: 800;">
                </div>
                <input type="text" name="about_page_stats[{{ $i }}][label]"
                       value="{{ $stat['label'] ?? '' }}" placeholder="Veículos Negociados"
                       style="width: 100%; padding: .5rem; border: 1px solid #d9e3f1; border-radius: .5rem; font-size: .85rem;">
            </div>
            @endforeach
        </div>

        {{-- ═══════════ ABA: EQUIPE ═══════════ --}}
        <div x-show="tab === 'team'" class="admin-card" style="margin-bottom: 1rem;">
            <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Equipe</h2>
            <p style="font-size: .8rem; color: #5f6f86; margin-bottom: 1rem;">Até 6 membros. Fotos podem ser enviadas após salvar (via botão de upload individual).</p>

            @foreach($teamRows as $i => $member)
            <div style="border: 1px solid #d9e3f1; border-radius: .75rem; padding: 1rem; margin-bottom: .75rem; background: #f9fafb;">
                <div style="display: flex; align-items: center; gap: .75rem; margin-bottom: .75rem;">
                    @if(filled($member['photo'] ?? ''))
                        <img src="{{ asset($member['photo']) }}" alt="" style="height: 48px; width: 48px; border-radius: 50%; object-fit: cover;">
                    @else
                        <div style="height: 48px; width: 48px; border-radius: 50%; background: #0f2f4d; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: .85rem;">
                            {{ $i + 1 }}
                        </div>
                    @endif
                    <span style="font-weight: 700; font-size: .85rem;">Membro #{{ $i + 1 }}</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: .5rem; margin-bottom: .5rem;">
                    <div>
                        <label style="font-size: .75rem; font-weight: 600;">Nome</label>
                        <input type="text" name="about_page_team[{{ $i }}][name]"
                               value="{{ $member['name'] ?? '' }}" placeholder="Nome completo"
                               style="width: 100%; padding: .45rem .6rem; border: 1px solid #d9e3f1; border-radius: .5rem; font-size: .85rem;">
                    </div>
                    <div>
                        <label style="font-size: .75rem; font-weight: 600;">Cargo</label>
                        <input type="text" name="about_page_team[{{ $i }}][role]"
                               value="{{ $member['role'] ?? '' }}" placeholder="Ex: Diretor Comercial"
                               style="width: 100%; padding: .45rem .6rem; border: 1px solid #d9e3f1; border-radius: .5rem; font-size: .85rem;">
                    </div>
                </div>

                <div style="margin-bottom: .5rem;">
                    <label style="font-size: .75rem; font-weight: 600;">Mini bio</label>
                    <textarea name="about_page_team[{{ $i }}][bio]" rows="2"
                              placeholder="Breve descrição..."
                              style="width: 100%; padding: .45rem .6rem; border: 1px solid #d9e3f1; border-radius: .5rem; font-size: .85rem;">{{ $member['bio'] ?? '' }}</textarea>
                </div>

                @if($setting->exists)
                <div x-data="{ uploading: false }" style="margin-top: .5rem;">
                    <label style="font-size: .75rem; font-weight: 600;">Foto</label>
                    <input type="file" accept="image/*"
                           @change="
                               uploading = true;
                               let fd = new FormData();
                               fd.append('photo', $event.target.files[0]);
                               fd.append('index', {{ $i }});
                               fd.append('_token', '{{ csrf_token() }}');
                               fetch('{{ route('admin.v2.about-page.upload-team-photo') }}', { method: 'POST', body: fd })
                                   .then(r => r.json())
                                   .then(d => { uploading = false; location.reload(); })
                                   .catch(() => { uploading = false; alert('Erro ao enviar foto'); });
                           "
                           style="font-size: .8rem; margin-top: .25rem;">
                    <span x-show="uploading" style="font-size: .75rem; color: #0f62fe;">Enviando...</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- ═══════════ ABA: DEPOIMENTOS ═══════════ --}}
        <div x-show="tab === 'testimonials'" class="admin-card" style="margin-bottom: 1rem;">
            <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Depoimentos</h2>
            <p style="font-size: .8rem; color: #5f6f86; margin-bottom: 1rem;">Até 6 depoimentos. Aceita texto e/ou vídeo do YouTube.</p>

            @foreach($testimonialRows as $i => $t)
            <div style="border: 1px solid #d9e3f1; border-radius: .75rem; padding: 1rem; margin-bottom: .75rem; background: #f9fafb;">
                <div style="display: flex; align-items: center; gap: .5rem; margin-bottom: .75rem;">
                    @if(filled($t['photo'] ?? ''))
                        <img src="{{ asset($t['photo']) }}" alt="" style="height: 40px; width: 40px; border-radius: 50%; object-fit: cover;">
                    @endif
                    <span style="font-weight: 700; font-size: .85rem;">Depoimento #{{ $i + 1 }}</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: .5rem; margin-bottom: .5rem;">
                    <div>
                        <label style="font-size: .75rem; font-weight: 600;">Nome</label>
                        <input type="text" name="about_page_testimonials[{{ $i }}][name]"
                               value="{{ $t['name'] ?? '' }}" placeholder="Nome"
                               style="width: 100%; padding: .45rem .6rem; border: 1px solid #d9e3f1; border-radius: .5rem; font-size: .85rem;">
                    </div>
                    <div>
                        <label style="font-size: .75rem; font-weight: 600;">Cargo</label>
                        <input type="text" name="about_page_testimonials[{{ $i }}][role]"
                               value="{{ $t['role'] ?? '' }}" placeholder="Cargo"
                               style="width: 100%; padding: .45rem .6rem; border: 1px solid #d9e3f1; border-radius: .5rem; font-size: .85rem;">
                    </div>
                    <div>
                        <label style="font-size: .75rem; font-weight: 600;">Empresa</label>
                        <input type="text" name="about_page_testimonials[{{ $i }}][company]"
                               value="{{ $t['company'] ?? '' }}" placeholder="Empresa"
                               style="width: 100%; padding: .45rem .6rem; border: 1px solid #d9e3f1; border-radius: .5rem; font-size: .85rem;">
                    </div>
                </div>

                <div style="margin-bottom: .5rem;">
                    <label style="font-size: .75rem; font-weight: 600;">Depoimento (texto)</label>
                    <textarea name="about_page_testimonials[{{ $i }}][text]" rows="3"
                              placeholder="O que o parceiro disse..."
                              style="width: 100%; padding: .45rem .6rem; border: 1px solid #d9e3f1; border-radius: .5rem; font-size: .85rem;">{{ $t['text'] ?? '' }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 100px; gap: .5rem; margin-bottom: .5rem;">
                    <div>
                        <label style="font-size: .75rem; font-weight: 600;">URL do Vídeo (YouTube)</label>
                        <input type="text" name="about_page_testimonials[{{ $i }}][video_url]"
                               value="{{ $t['video_url'] ?? '' }}" placeholder="https://youtube.com/watch?v=..."
                               style="width: 100%; padding: .45rem .6rem; border: 1px solid #d9e3f1; border-radius: .5rem; font-size: .85rem;">
                    </div>
                    <div>
                        <label style="font-size: .75rem; font-weight: 600;">Nota (1-5)</label>
                        <select name="about_page_testimonials[{{ $i }}][rating]"
                                style="width: 100%; padding: .45rem .6rem; border: 1px solid #d9e3f1; border-radius: .5rem; font-size: .85rem;">
                            @for($r = 5; $r >= 1; $r--)
                                <option value="{{ $r }}" {{ (int)($t['rating'] ?? 5) === $r ? 'selected' : '' }}>{{ $r }} ★</option>
                            @endfor
                        </select>
                    </div>
                </div>

                @if($setting->exists)
                <div x-data="{ uploading: false }" style="margin-top: .5rem;">
                    <label style="font-size: .75rem; font-weight: 600;">Foto do autor</label>
                    <input type="file" accept="image/*"
                           @change="
                               uploading = true;
                               let fd = new FormData();
                               fd.append('photo', $event.target.files[0]);
                               fd.append('index', {{ $i }});
                               fd.append('_token', '{{ csrf_token() }}');
                               fetch('{{ route('admin.v2.about-page.upload-testimonial-photo') }}', { method: 'POST', body: fd })
                                   .then(r => r.json())
                                   .then(d => { uploading = false; location.reload(); })
                                   .catch(() => { uploading = false; alert('Erro ao enviar foto'); });
                           "
                           style="font-size: .8rem; margin-top: .25rem;">
                    <span x-show="uploading" style="font-size: .75rem; color: #0f62fe;">Enviando...</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- ═══════════ ABA: GALERIA ═══════════ --}}
        <div x-show="tab === 'gallery'" class="admin-card" style="margin-bottom: 1rem;">
            <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Galeria de Fotos</h2>

            {{-- Fotos existentes --}}
            @if(count($gallery) > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: .75rem; margin-bottom: 1rem;">
                @foreach($gallery as $photo)
                <div x-data="{ deleting: false }" style="position: relative; border-radius: .75rem; overflow: hidden; aspect-ratio: 1;">
                    <img src="{{ asset($photo) }}" alt="Galeria" style="width: 100%; height: 100%; object-fit: cover;">
                    <button type="button"
                            x-show="!deleting"
                            @click="
                                if (!confirm('Excluir esta foto?')) return;
                                deleting = true;
                                let fd = new FormData();
                                fd.append('path', '{{ $photo }}');
                                fd.append('_token', '{{ csrf_token() }}');
                                fetch('{{ route('admin.v2.about-page.delete-gallery') }}', { method: 'POST', body: fd })
                                    .then(() => location.reload())
                                    .catch(() => { deleting = false; alert('Erro'); });
                            "
                            style="position: absolute; top: 4px; right: 4px; background: rgba(220,38,38,.9); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center;">
                        ✕
                    </button>
                    <span x-show="deleting" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,.5); color: white; font-size: .75rem;">Excluindo...</span>
                </div>
                @endforeach
            </div>
            @else
            <p style="font-size: .85rem; color: #5f6f86; margin-bottom: 1rem;">Nenhuma foto na galeria ainda.</p>
            @endif

            {{-- Upload de novas fotos --}}
            <div x-data="{ uploading: false, count: 0 }">
                <label style="font-weight: 700; font-size: .85rem;">Adicionar fotos</label>
                <input type="file" accept="image/*" multiple
                       @change="
                           if ($event.target.files.length === 0) return;
                           uploading = true;
                           count = $event.target.files.length;
                           let fd = new FormData();
                           for (let f of $event.target.files) fd.append('photos[]', f);
                           fd.append('_token', '{{ csrf_token() }}');
                           fetch('{{ route('admin.v2.about-page.upload-gallery') }}', { method: 'POST', body: fd })
                               .then(r => r.json())
                               .then(d => { uploading = false; location.reload(); })
                               .catch(() => { uploading = false; alert('Erro ao enviar fotos'); });
                       "
                       style="display: block; margin-top: .35rem; font-size: .85rem;">
                <span x-show="uploading" style="font-size: .8rem; color: #0f62fe; margin-top: .25rem;">Enviando <span x-text="count"></span> foto(s)...</span>
                <p style="font-size: .75rem; color: #5f6f86; margin-top: .25rem;">Selecione múltiplas fotos. Máx 3MB cada. Recomendado: 800x800px.</p>
            </div>
        </div>

        {{-- ═══════════ ABA: VÍDEO ═══════════ --}}
        <div x-show="tab === 'video'" class="admin-card" style="margin-bottom: 1rem;">
            <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Vídeo Institucional</h2>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 700; font-size: .85rem;">URL do YouTube</label>
                <input type="text" name="about_page_video_url"
                       value="{{ old('about_page_video_url', $setting->about_page_video_url ?? '') }}"
                       placeholder="https://www.youtube.com/watch?v=..."
                       style="width: 100%; padding: .6rem .75rem; border: 1px solid #d9e3f1; border-radius: .75rem; margin-top: .35rem;">
                <p style="font-size: .75rem; color: #5f6f86; margin-top: .25rem;">Cole a URL completa do vídeo do YouTube. Aceita youtube.com/watch e youtu.be.</p>
            </div>

            @php
                $previewUrl = $setting->about_page_video_url ?? '';
                $previewId = null;
                if (filled($previewUrl) && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $previewUrl, $pm)) {
                    $previewId = $pm[1];
                }
            @endphp
            @if($previewId)
            <div style="margin-top: .5rem;">
                <p style="font-size: .8rem; font-weight: 700; margin-bottom: .35rem;">Preview:</p>
                <div style="max-width: 480px; border-radius: .75rem; overflow: hidden;">
                    <div style="position: relative; width: 100%; padding-bottom: 56.25%;">
                        <iframe src="https://www.youtube.com/embed/{{ $previewId }}" style="position: absolute; inset: 0; width: 100%; height: 100%;" frameborder="0" allowfullscreen loading="lazy"></iframe>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- BOTÃO SALVAR --}}
        <div style="display: flex; gap: .75rem; margin-top: 1rem;">
            <button type="submit" class="admin-btn-primary" style="padding: .65rem 2rem; font-size: .9rem;">
                💾 Salvar Alterações
            </button>
            <a href="{{ route('sobre-nos') }}" target="_blank" class="admin-btn-soft" style="padding: .65rem 1.5rem; font-size: .9rem;">
                👁 Ver Página Pública
            </a>
        </div>
    </form>
</div>
@endsection
