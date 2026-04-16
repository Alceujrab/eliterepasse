# Prompt: Landing Publica B2B estilo Portal do Lojista (pre-login)

Atue como Arquiteto(a) Senior de Frontend/Laravel e desenvolva a **landing publica (antes do login)** de um portal B2B de compra de veiculos para lojistas, inspirada na estrutura funcional do Portal do Lojista da Localiza.

## Objetivo
Criar uma home publica com foco em conversao para cadastro de lojistas, mantendo identidade visual:
- Azul (cor principal)
- Branco (base)
- Laranja (CTAs)

Nao implementar area interna pos-login (essa ja existe).

## Stack obrigatoria
- Laravel Blade + Livewire (somente render da home)
- Tailwind CSS
- Alpine.js para interacoes leves (menu mobile e accordion FAQ)
- Totalmente responsivo (mobile first)

## Estrutura obrigatoria da landing
1. Header fixo
- Logo
- Links de ancora: Modelos, Vantagens, Como Funciona, FAQ
- Botoes: Entrar e Cadastre-se
- Menu hamburguer funcional no mobile

2. Hero principal
- Headline forte para B2B
- Subheadline explicando proposta de valor
- 2 CTAs: Cadastre-se agora e Ja tenho conta
- Cards curtos de beneficios
- Imagem principal com destaque visual

3. Secao de modelos em destaque
- Grid de cards de veiculos (imagem, nome, tipo)
- Possibilidade de selo de destaque
- CTA: Quero acessar mais carros

4. Secao de vantagens
- 3 blocos: Laudo, Quilometragem real, Preco abaixo da FIPE

5. Secao "Como funciona"
- 4 passos numerados:
  1) Cadastro
  2) Acesso
  3) Escolha dos carros
  4) Compra online
- CTA final da secao

6. Secao de gestao
- 3 cards com:
  - Acompanhar pedidos
  - Consultar dados do veiculo
  - Financeiro sem friccao
- Bloco de destaque para equipe especializada

7. FAQ
- Lista de perguntas frequentes em accordion
- Interacao via Alpine.js

8. Bloco para quem nao possui CNPJ
- Aviso de uso exclusivo para PJ
- Link para alternativa de estoque de varejo (externo)

9. CTA final
- Chamada de fechamento com botoes de Cadastro e Entrar

10. Footer
- Informacoes institucionais
- Links importantes
- Direitos autorais

11. Botao flutuante de WhatsApp
- Fixo no canto inferior direito
- Numero vindo de configuracao

## Requisitos de UX/UI
- Visual premium, limpo e moderno
- Tipografia forte para titulos
- Cards com bordas suaves e sombras discretas
- Hero com gradiente e profundidade visual
- Alto contraste para acessibilidade
- Tamanho de toque adequado no mobile

## SEO minimo
- title e meta description
- Open Graph (title, description, image, url)
- theme-color

## Regras de implementacao
- Nao alterar rotas de area logada
- Nao remover funcionalidades existentes de login/cadastro
- Preservar possibilidade de textos dinâmicos via configuracao da landing
- Evitar copia literal de textos e imagens de terceiros

## Entregaveis
- Blade da landing publica completa
- Ajustes de layout base para metadados
- Codigo limpo, organizado, com comentarios apenas quando necessario
- Resultado visual pronto para uso em desktop e mobile
