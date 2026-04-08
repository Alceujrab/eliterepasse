# Manutenção Diária do Elite Repasse

Como cuidar da saúde da plataforma no dia a dia.

## Os "Limpadores" do Sistema (Em caso de Lentidão)

Se o sistema ficar estranho, não mostrar os ícones ou "cair", geralmente não é vírus. É apenas o cérebro dele ("Cache") tentando se lembrar de uma versão velha.
Se você estiver rodando em Servidor Local ou via cPanel Terminal, rode:

```bash
php artisan optimize:clear
```
Isso vai jogar toda a memória cache do sistema no lixo e obrigar ele a re-ler o banco e as configurações novinhas.

## Gerenciando os Permissões
- **Como Lojistas entram?** A aba **Usuários** no painel do administrador (`/admin/users`) exibe uma listagem visual rica. Os usuários entram inicialmente como "Pendente" via Google OAuth ou Registro. Cabeças vão rolar se você não clicar em "Aprovar"! O lojista recebe um WhatsApp na hora que você aprovar dizendo para ele olhar os carros!
- **Posso bloquear lojistas chatos?** Sim! Selecione o Lojista na mesma aba e clique no botão vermelho "Bloquear". Isso chuta a conta dele no mesmo segundo e proíbe a verificação do e-mail. Tudo automatizado via Alpine.JS + Laravel.

## A Importância do `.env`
O arquivo oculto chamado `.env` guarda a chave da porta! Se você perder as configurações:
- `RECAPTCHA_SECRET_KEY=` : O ReCaptcha Invisivel v3 pára e dá falso positivo (Robôs passam livres e Lojistas Reais sofrem Timeout).
- `EVOLUTION_API_TOKEN=` : Sem esse Token Global da EvolutionAPI, os Webhooks falham silenciosamente na esteira. O usuário vai ser aprovado no CPanel, mas a Tabela não disparará a Queue do WhatsApp. Mantenha isso em cofre.
