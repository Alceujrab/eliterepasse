# QA Admin - Cenarios Pendentes (Fechamento 100%)

Data: 2026-04-14
Escopo: fechar cobertura dos botoes pendentes no admin legado.

## Objetivo
Validar os botoes que dependem de estado de dados:
- Gerar Fatura
- Confirmar Pagamento
- Verificar Documento

## Premissas de seguranca
- Executar primeiro em homologacao/local.
- Evitar usar pedidos reais de producao para testes destrutivos.
- Registrar numero do pedido/documento alterado para rollback.

## Cenario 1 - Gerar Fatura

### Pre-condicao
Ter um pedido com:
- status = confirmado
- sem registro financeiro associado

### Como obter pre-condicao (sem seed)
1. Acessar /admin/orders.
2. Selecionar um pedido pendente e clicar em Confirmar.
3. Reabrir listagem e localizar o pedido agora confirmado.
4. Verificar se o botao Gerar Fatura aparece na linha do pedido.

### Validacao esperada
- Botao Gerar Fatura visivel.
- Ao clicar, modal abre com campos:
  - descricao
  - valor
  - forma_pagamento
  - data_vencimento
  - observacoes

## Cenario 2 - Confirmar Pagamento

### Pre-condicao
Ter um pedido com:
- status = faturado
- financial.status = em_aberto

### Como obter pre-condicao
1. Partindo do Cenario 1, clicar em Gerar Fatura e salvar.
2. Voltar para /admin/orders.
3. Localizar o pedido faturado.
4. Verificar se o botao Confirmar Pagamento aparece.

### Validacao esperada
- Botao Confirmar Pagamento visivel.
- Ao confirmar:
  - financial.status muda para pago
  - pedido muda para pago
  - notificacao de sucesso exibida

## Cenario 3 - Verificar Documento

### Pre-condicao
Ter um documento com:
- status = pendente

### Como obter pre-condicao
1. Acessar /admin/documents/create.
2. Preencher campos obrigatorios.
3. Anexar arquivo valido (PDF/JPG/PNG).
4. Salvar mantendo status pendente.
5. Voltar para /admin/documents.

### Validacao esperada
- Botao Verificar visivel na linha do documento pendente.
- Ao clicar:
  - status passa para verificado
  - verificado_por preenchido
  - verificado_em preenchido

## Checklist de fechamento
- [x] Gerar Fatura validado
- [x] Confirmar Pagamento validado
- [x] Verificar Documento validado
- [x] Evidencia (URL, registro e timestamp) anexada no checklist principal

## Resultado final
Cobertura funcional concluida em 100% para os botoes pendentes do admin legado.

## Rollback sugerido
Se o ambiente for compartilhado:
1. Marcar registros de teste com observacao "QA-2026-04-14".
2. Ao final, excluir ou arquivar pedidos/documentos criados para teste.
3. Se nao for possivel excluir, manter trilha documentada com IDs afetados.
