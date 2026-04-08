# Cadastro de Funcionários — PHP + PostgreSQL

## Requisitos

- PHP 5.6+ (ou PHP 7/8) com extensão `pdo_pgsql`
- PostgreSQL 9.5+
- Servidor web: Apache (com mod_rewrite) ou PHP built-in server

---

## Instalação

### 1. Configurar o Banco de Dados

```bash
# Criar o banco de dados
createdb cadastro_funcionarios

# Executar o script SQL
psql -d cadastro_funcionarios -f banco.sql
```

### 2. Configurar a Conexão

Edite o arquivo `config/database.php` com suas credenciais:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'cadastro_funcionarios');
define('DB_USER', 'postgres');   // ← seu usuário
define('DB_PASS', 'postgres');   // ← sua senha
```

### 3. Iniciar o Servidor

**Opção A — PHP Built-in Server:**
```bash
php -S localhost:8080
```
Acesse: http://localhost:8080

**Opção B — Apache (XAMPP/WAMP):**
- Copie a pasta para `htdocs/cadastro_funcionarios`
- Acesse: http://localhost/cadastro_funcionarios

---

## Login Padrão

| Campo    | Valor     |
|----------|-----------|
| Usuário  | `admin`   |
| Senha    | `password`|

> A senha `password` está hasheada com `password_hash()` no `banco.sql`.
> Para criar uma nova senha, use `password_hash('sua_senha', PASSWORD_DEFAULT)`.

---

## Estrutura de Arquivos

```
cadastro_funcionarios/
├── index.php          ← Página de login
├── listagem.php       ← Listagem + busca + paginação
├── cadastro.php       ← Cadastro e edição de funcionários
├── visualizar.php     ← Visualizar detalhes
├── excluir.php        ← Excluir funcionário (action)
├── logout.php         ← Logout
├── banco.sql          ← Script de criação do banco
├── config/
│   ├── database.php   ← Configuração do PostgreSQL (PDO)
│   └── session.php    ← Helpers de sessão/autenticação
├── includes/
│   └── navbar.php     ← Navbar compartilhada
└── assets/
    └── css/
        └── style.css  ← Estilos globais
```

---

## Funcionalidades

- ✅ Autenticação com sessão PHP
- ✅ Cadastro de funcionários (nome, cargo, e-mail, telefone, situação)
- ✅ Edição de funcionários
- ✅ Exclusão com confirmação via modal
- ✅ Listagem com busca (nome, cargo, e-mail)
- ✅ Paginação (10 por página)
- ✅ Badges de status (Ativo/Inativo)
- ✅ Máscara de telefone
- ✅ Design fiel ao layout original
