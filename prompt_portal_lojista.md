# Prompt de Desenvolvimento: Sistema de Repasse de Veículos B2B (Clone Portal do Lojista)

**Objetivo Central:**
Você atuará como um Especialista em Laravel e Arquitetura Full-Stack. O objetivo é criar do zero um sistema B2B completo de e-commerce de veículos seminovos no atacado, com arquitetura robusta usando a versão mais recente do Laravel (Laravel 11+), replicando com máxima fidelidade as funções, o design e toda a estrutura de negócio extraída do "Portal do Lojista" da Localiza (<https://portaldolojista.localiza.com/>).

O sistema conectará a vendedora (Admin/Concessionária) aos lojistas de automóveis (compradores logados), oferecendo vitrine controlada, gestão de fluxo financeiro, documentação veicular e chamados de suporte.

---

## 1. Stack Tecnológico Mandatório

**sistema deve ser todo pensado para ser mobile e responsivo, menus, cards, tudo deve ser pensado para ser usado em celular ou tablet, onde no mobile se use os dedos em vez de mouse, os botões devem ser grandes e fáceis de clicar, logomarca no topo tanto para versão mobile quanto versão desktop, cores predominantes azul escuro e branco, com laranja para CTAs, use glassmorphism, bordas sutis e fontes modernas (Inter/Roboto), criar favicon, icones para sistema android e IOS, ja orientar o sistema todo para SEO, usar schema.org, meta tags, open graph, etc. ,pesquisar os modulos adicionais na documentação do laravael3 afim de utilizar o que for interessante ao sistema, como inteligencia artificial,Socialite do Laravel, Laravel Horizon,Laravel Pennant,O Laravel Telescope, SDK de IA do Laravel, outros que possam ser uteis. criar skills para documentar tudo no sistema, como tutoriais, videos, etc., as skills devem ser criadas em portugues do brasil, e devem ser fáceis de entender e seguir, como se fosse um passo a passo para um iniciante em programação. o sistema sera instalado em servidor comartilhado usando CPANEL,.**

* **Backend:** Laravel (Latest / 13+). Programação 100% orientada a objetos usando Repositories/Services para deixar os Controllers limpos.
* **Frontend (Selecione uma opção ao iniciar):**
  * *Opção A (Recomendada para Velocidade):* Laravel Livewire 3 + Alpine.js.

* **Design & UI/UX:** TailwindCSS. O design **deve ser espetacular, limpo e premium**. Utilize paletas de núcleo: Azul Escuro (`1f5a7c`) e Branco, com laranja para *Call to Actions* (CTAs). Incorporar Glassmorphism, bordas sutis e fontes modernas (Inter/Roboto).
* **Banco de Dados:** MySQL, fazendo uso pesado de campos `JSON/JSONB` para lista de acessórios estruturados e links de mídia do veículo.
* **Autenticação:** Laravel Breeze ou Jetstream (implementando lógica de Multi-Tenancy se o lojista for dono de várias lojas).
* **Busca Semântica:** Implementar filtros AJAX performáticos sem recarregamento da página (podendo usar Laravel Scout).

---

## 2. Base de Dados: Estrutura Principal (Models & Migrations)

Crie imediatamente as migrations detalhadas, models com os *fillables*, *casts* adequados, *factories* e relacionamentos para as entidades listadas:

1. **Users (`User`) - Lojista ou Admin:**
    Campos `id`, `name`, `foto`,`cpf`, `email`, `phone`, `password`, `is_admin`.
2. **Lojas/Empresas (`Company`):**
    Campos `id`, `razao_social`,`imagem`, `cnpj`,`Whatsapp`, `inscricao_estadual`, `address`, `city`, `state`, `zipcode`.
    *(Obs: Relacionamento N:N com User através de uma tabela Pivot `company_user`)*.
3. **Veículos (`Vehicle`):**
    Campos de placa `plate` (com obfuscador no getter publico), marca `brand`, `model`, versão `version`, `manufacture_year` (Ano Fab.), `model_year` (Ano Mod.), `mileage` (KM), `fuel_type`, `transmission`, cor `color`, `category`.
    *Campos de negócio:* `sale_price` (Preço), `fipe_price` (FIPE), `profit_margin`.
    *JSON:* `accessories` (Ar, Direção, Vidro, etc), `media` (URLs das imagens), `location` (Endereço do pátio atual).
    *Status:* `available`, `reserved`, `sold`.
4. **Pedidos/Contratos (`Order`):**
    `company_id`, `status` (aguardando_pgto, faturado, entregue), `total_amount`. Pivot para vincular quais carros foram comprados `order_vehicle`.
5. **Financeiro (`Financial`):**
    `order_id`, `invoice_url` (PDF Nota Fiscal), `boleto_url` (Link/PDF boleto), `digitable_line` (Linha do Boleto para copiar), `status`.
6. **Chamados/Tickets (`Ticket` e `TicketMessage`):**
    Para contato direto. `user_id`, `vehicle_id` (Nulável), `type` (Ex: transferencia_crv, logistica, reclamacao), `status` (aberto, andamento, fechado).

---

## 3. Escopo de Funcionalidades do Lojista (Módulos a Implementar)

### Módulo de Autenticação e Entrada

* **Login Customizado:** Acesso por login (e-mail ou cpf) e senha; bloqueio total das páginas com middleware `auth`. Sem criação de conta aberta.
* **Troca de Lojas (Seletor):** Após o login, exigir um seletor modal caso o usuário represente mais de um CNPJ, carregando a variável de sessão da Empresa em uso.

### Módulo Vitrine e Dashboard de Veículos (`/carros`)

* **Header Padrão:** Busca livre (placa/modelo), Notificações, Indicador do "Nível do Usuário" (Ex: Bronze/Prata) e Avatar de conta.
* **Filtros Laterais (Tempo Real):** Filtros laterais com checkbox para Marcas, Câmbio automático/manual, Tipos de Veículos (Hatch, SUV, Sedan), Faixas de Preço e KM. Tudo recarregando apenas a lista central.
* **Card Veicular (Grind):** Exibir primeira imagem do carousel, Dados vitais, tags ("Promoção", "Chegou Hoje"), preço e indicador visual da FIPE.
* **Detalhe do Veículo (`/carro/{id}`):** Visualização completa do anúncio, listagem detalhada do checklist de acessórios (Ex: [x] Ar [x] Trava [ ] Teto). Visualizador de Imagens, Botão para fazer donwload de Laudo PDF de vistoria e CTA flutuante grandioso verde: "**Tenho Interesse**".

### Módulo de "Meus Pedidos" (`/meus-pedidos`)

* Gerenciamento pós-clique de interesse. Visualização em formato de lista dos carros adquiridos, exibindo linha do tempo clara dos status (Aguardando Retirada, Entregue, Cancelado).

### Módulo Financeiro (`/financeiro`)

* Visualizador dos débitos (carros ou faturas). O lojista pode baixar notas fiscais ou a segunda via do boleto bancário, podendo copiar o código do boleto de forma interativa.

### Módulo "Meus Documentos" (`/documentos`)

* Interface onde o Lojista pode baixar rapidamente procurações modelo, documentos fiscais gerais, além de exibir atalhos para download das imagens do CRV dos carros comprados na plataforma.

### Módulo Suporte - "Meus Chamados" (`/meus-chamados`)

* Permitir criar chamados referenciando placas de carros que foram adquiridos para reclamar de inconformidades, laudos, trâmites de transferência. Interface de Chat em Histórico da Conversa com Respostas do Admin.

### Módulo de Conta (`/minha-conta`)

* Alteração de senha corporativa.
* Exibição dos dados do contato humano comercial (`Representante Comercial Designado`), com botão para abrir diretamente o **WhatsApp web** do gerente de relacionamento.

---

## 4. Instruções de Execução (O que fazer primeiro)

Para manter a ordem de desenvolvimento limpa e sem refações:

1. Inicie instalando o framework Laravel limpo e criando/revisando o schema das bases de dados baseadas nas informações do item (2). Crie seeds básicos para conseguirmos testar inicialmente com o lojista de exemplo "<alceujr.ab@gmail.com>".
2. Configure a tela de login inicial base e construa o esqueleto do layout principal que abrigará todos os módulos (Grid, Header, Sidebar).
3. Construa a tela de vitrine de carros e detalhes do carro, pois é o núcleo comercial da ferramenta. Execute isso certificando-se que a experiência UI é rica, usando Tailwind apropriadamente.

Ao seu sinal, envie o schema de classes de bancos de dados para começarmos.
