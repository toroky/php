<?php
// index.php — Página de Login

require_once 'config/database.php';
require_once 'config/session.php';

if (isLoggedIn()) {
    header('Location: listagem.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $senha    = $_POST['senha'] ?? '';

    if ($username === '' || $senha === '') {
        $erro = 'Preencha usuário e senha.';
    } else {
        try {
            $pdo  = getConnection();
            $stmt = $pdo->prepare("SELECT id, nome, senha FROM usuarios WHERE username = :u LIMIT 1");
            $stmt->execute([':u' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($senha, $user['senha'])) {
                $_SESSION['usuario_id']   = $user['id'];
                $_SESSION['usuario_nome'] = $user['nome'];
                header('Location: listagem.php');
                exit;
            } else {
                $erro = 'Usuário ou senha incorretos.';
            }
        } catch (Exception $e) {
            $erro = 'Erro ao acessar o banco de dados.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionários — Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome via CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-user-tie icon-user"></i>
                <h1>Cadastro de<br>Funcionários</h1>
            </div>

            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php" autocomplete="off">
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" placeholder="Usuário"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                </div>
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" name="senha" placeholder="Senha" required>
                </div>
                <button type="submit" class="btn-entrar">Entrar</button>
            </form>

            <hr class="login-divider">
            <div class="login-forgot">
                <a href="#">Esqueci minha senha</a>
            </div>
        </div>
    </div>
</body>
</html>
