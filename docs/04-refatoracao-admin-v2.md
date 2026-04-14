# Refatoracao Total do Admin (Sem Filament v4)

## Objetivo
Migrar o painel administrativo de `/admin` (Filament v4) para `/painel-admin` (Blade + Tailwind + controllers dedicados), com UX moderna, responsiva e de manutencao simples.

## Diagnostico Atual

### URL e ponto central
- Painel legado: `/admin`
- Provider atual: `App\Providers\Filament\AdminPanelProvider`
- Dependencia direta em `filament/filament` no `composer.json`

### Cobertura de links do menu (rotas detectadas)
Mapeado via `php artisan route:list --path=admin`:
- dashboard
- clients
- vehicles
- vehicle-reports
- orders
- contracts
- documents
- tickets
- relatorios
- gestao-financeira
- whatsapp-instancias
- whatsapp-inbox
- email-templates
- landing-settings
- central-notificacoes
- configuracoes-gerais
- evolution-instances
- general-documents

### Cobertura de acoes (botoes) detectadas no legado
Mapeado por busca em `app/Filament/**`:
- Criar/Editar/Visualizar/Excluir em multiplos recursos
- Pedidos: confirmar, gerar_contrato, gerar_fatura, confirmar_pagamento, cancelar
- Contratos: enviarWhatsApp, copiarLink
- Documentos: visualizar, verificar, rejeitar, anexar arquivo, download
- Relatorios: exportar_csv, exportar_pdf
- Tickets: responder, atribuir
- Veiculos: marcar_disponivel, marcar_vendido, anexar imagens
- Evolution/WhatsApp: conectar, qrcode, enviar_teste

## Arquitetura Alvo (Admin v2)

### Stack
- Backend: Laravel controllers + middleware admin
- Frontend: Blade + Tailwind (layout responsivo desktop/mobile)
- Estado de tela: Livewire apenas onde agregar valor (formularios complexos e tabelas com filtros)
- Autorizacao: middleware `EnsureAdmin` e policies por modulo

### Principios
- Sem lock-in de framework de admin
- Componentes visuais reaproveitaveis
- Separacao por dominio de negocio
- Compatibilidade com servicos existentes (NotificationService, ContractService, EvolutionService)

## Plano de Migracao (Fases)

### Fase 1 (concluida nesta entrega)
- Criacao do shell do novo admin em paralelo:
  - Rota: `/painel-admin`
  - Middleware: `EnsureAdmin`
  - Dashboard moderno com KPIs
  - Pagina de modulo com mapa de acoes e link para legado
  - Menu responsivo para desktop/mobile

### Fase 2 (prioridade alta)
- Migrar modulos de maior valor operacional:
  - `orders`, `contracts`, `documents`, `tickets`
- Entregas por modulo:
  - listagem com filtros
  - acoes principais (confirmar, gerar contrato/fatura, anexar, etc.)
  - validacoes e mensagens de feedback
  - testes feature

#### Progresso atual da Fase 2
- `orders` entregue no Admin v2 com rotas reais e acoes operacionais:
  - listar + filtro por status/busca
  - confirmar pedido
  - gerar contrato
  - gerar fatura
  - confirmar pagamento
  - cancelar pedido
- `contracts` entregue no Admin v2 com rotas reais e acoes operacionais:
  - listar + filtro por status/busca
  - enviar link de assinatura por WhatsApp
  - copiar link de assinatura

### Fase 3
- Migrar modulos de configuracao e suporte:
  - `clients`, `vehicles`, `vehicle-reports`, `financeiro`, `relatorios`
- Exportacao PDF/CSV no novo fluxo

### Fase 4
- Migrar modulos administrativos complementares:
  - `email-templates`, `landing-settings`, `whatsapp-*`, `evolution-instances`, `central-notificacoes`

### Fase 5 (desligamento Filament)
- Congelar novas alteracoes em `app/Filament`
- Redirecionar `/admin` para `/painel-admin`
- Remover provider Filament de `bootstrap/providers.php`
- Remover pacote `filament/filament` do `composer.json`
- Revisar `User` para remover contrato `FilamentUser`
- Rodar bateria final de testes e smoke test

## Checkpoints de Qualidade
- Layout validado em mobile e desktop
- Nenhuma regressao de regra de negocio
- Cobertura de fluxos criticos:
  - pedido -> contrato -> fatura -> pagamento
  - validacao de documentos
  - atendimento de tickets
- Logs de erro monitorados por modulo apos deploy

## Riscos e Mitigacoes
- Risco: divergencia entre fluxos novo x legado
  - Mitigacao: migracao em paralelo por modulo + feature tests
- Risco: regressao em servicos de notificacao
  - Mitigacao: manter services existentes e trocar apenas camada de interface
- Risco: indisponibilidade operacional
  - Mitigacao: corte somente apos homologacao completa por modulo
