# Instalação em Servidor Compartilhado (cPanel / Hostgator / Hostinger)

Projetos Laravel normalmente exigem um Servidor VPS (como AWS, DigitalOcean), mas sabemos que para começar um servidor **cPanel** é mais barato e comum. Segue a "Skill" de como não quebrar a cabeça publicando o Elite Repasse lá!

## Passo a Passo para o Iniciante

### 1. Preparar os Arquivos (Build local)

No seu computador, antes de enviar os arquivos, precisamos gerar os arquivos de CSS minificados.
Abra o terminal do seu VSCode e digite:

```bash
npm run build
```

*(Isso vai gerar os arquivos finais dentro da pasta `public/build`)*

### 2. Comprimir o Projeto

Crie um arquivo `.zip` cobrindo **TODAS** as pastas do diretório `eliterepasse`, **exceto**:

- `node_modules` (Ela é gigante e inútil em produção)

*(NÃO exclua a pasta `vendor`, ela é o coração do PHP)*.

### 3. Subir no cPanel

1. Entre no cPanel e vá em **Gerenciador de Arquivos**.
2. O cPanel usa uma pasta mágica chamada `public_html`. Todo o conteúdo visual deve ficar acessível por ali.
3. No entanto, por segurança, **nunca jogue o coração do Laravel dentro da `public_html`**.
4. Crie uma pasta um nível *antes* da `public_html`, por exemplo: `/api_elite`.
5. Faça o Upload do seu arquivo `.zip` dentro da `/api_elite` e clique em Extrair.

### 4. Apontar o Site (Symlink)

O seu domínio público (`meusite.com.br`) lê da pasta `public_html`. O pulo do gato é:
Mova *TODO* o conteúdo que está dentro de `/api_elite/public` e jogue para dentro da `/public_html`!!

Neste momento, edite o arquivo `index.php` que você colou na `public_html`. Mude estas linhas:

```php
require __DIR__.'/../api_elite/vendor/autoload.php';
$app = require_once __DIR__.'/../api_elite/bootstrap/app.php';
```

Isso liga seu site "público" ao cérebro do Laravel que ficou escondido atrás de um cofre.

### 5. Banco de Dados e Ambiente

1. No cPanel, vá em **Bancos de Dados MySQL** e crie um Banco, um Usuário e uma Senha. Dê *Todos os Privilégios*.
2. Renomeie o arquivo `.env.example` dentro da pasta `/api_elite` para `.env`.
3. Preencha as credenciais:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://meusite.com.br

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 6. Migrações e Link Storage

Como você não tem terminal fácil em cPanel compartilhado, crie um arquivo chamado `instalar.php` na sua `public_html` contendo:

```php
<?php
// Roda o Symlink das imagens do carro
Artisan::call('storage:link');
// Instala o banco limpo e enche os primeiros carros (Seed)
Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
echo "Tudo Pronto!";
?>
```

Acesse `meusite.com.br/instalar.php`, veja o sucesso, e depois DELETA ELA PARA NINGUÉM APAGAR SEU BANCO DE DADOS!

Pronto, seu site corporativo vai acender luz verde!
