# Estrutura e Ambiente do Elite Repasse

Bem-vindo à documentação inicial (Skill) do portal B2B **Elite Repasse**. Este guia foi feito passo a passo para iniciantes em programação ou gerentes de projeto.

## O que é este projeto?
Este portal é um sistema de "repasse de veículos" voltado para Lojistas.
- **Frontend** (A cara do site): Escrito em Blade, TailwindCSS, e Livewire v3 (que faz as ações atualizarem sem carregar a página).
- **Backend**: Laravel 11.
- **Admin**: Criado com Filament v3 (Acesso em `seusite.com/admin`).

## Pastas Importantes para Iniciantes

1. `app/Models`: Onde definimos os itens do banco de dados (Ex: `Vehicle.php`, `User.php`).
2. `app/Livewire`: Os Controladores da Vitrine, Dashboard do Lojista e Pedidos.
3. `app/Filament/Resources`: A inteligência por trás do Painel de Administração (`seusite.com/admin`). Se quiser mudar um campo no cadastro de carros, é aqui no `VehicleForm`.
4. `resources/views/livewire`: Os arquivos HTML visuais correspondentes às telas dos clientes. (Ex: `vitrine.blade.php`, `meus-pedidos.blade.php`).
5. `routes/web.php` e `routes/auth.php`: Onde dizemos qual "URL" abre qual tela.

## Como fazer Pequenas Alterações

**1. Trocar a cor de fundo da Vitrine:**
Acesse `resources/views/livewire/vitrine.blade.php` e altere a classe do `div` principal. O TailwindCSS define cores usando palavras simples como `bg-gray-100` ou hexadecimais `bg-[#1a3a5c]`.

**2. Alterar as regras do Botão do WhatsApp do Admin:**
As automações ("Webhooks") da Evolution API estão no controlador `app/Http/Controllers/EvolutionWebhookController.php`.

**3. Mudar o conteúdo do PDF do Contrato Automático:**
Acesse `resources/views/pdf/contrato.blade.php`. É um HTML puro desenhado como se fosse uma folha de papel. Basta editar os textos ali dentro.

No próximo documento, veja como Subir este site para o cPanel!
