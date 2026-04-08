<?php
// cadastro.php — Cadastro e edição de funcionários

require_once 'config/database.php';
require_once 'config/session.php';
requireLogin();

$pdo = getConnection();
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Carregar dados para edição
$funcionario = null;
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM funcionarios WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $funcionario = $stmt->fetch();
    if (!$funcionario) {
        header('Location: listagem.php');
        exit;
    }
}

$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome']     ?? '');
    $cargo    = trim($_POST['cargo']    ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $situacao = $_POST['situacao']      ?? 'Ativo';
    $postId   = (int)($_POST['id']      ?? 0);

    // Validações
    if ($nome === '')  $erros[] = 'Nome é obrigatório.';
    if ($cargo === '') $erros[] = 'Cargo é obrigatório.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'E-mail inválido.';
    }
    if (!in_array($situacao, ['Ativo', 'Inativo'])) $situacao = 'Ativo';

    if (empty($erros)) {
        try {
            if ($postId > 0) {
                // Atualizar
                $stmt = $pdo->prepare("
                    UPDATE funcionarios
                    SET nome=:nome, cargo=:cargo, email=:email,
                        telefone=:tel, situacao=:sit, atualizado_em=NOW()
                    WHERE id=:id
                ");
                $stmt->execute([
                    ':nome'  => $nome,
                    ':cargo' => $cargo,
                    ':email' => $email,
                    ':tel'   => $telefone,
                    ':sit'   => $situacao,
                    ':id'    => $postId,
                ]);
                $_SESSION['msg']      = 'Funcionário atualizado com sucesso!';
                $_SESSION['msg_tipo'] = 'success';
            } else {
                // Inserir
                $stmt = $pdo->prepare("
                    INSERT INTO funcionarios (nome, cargo, email, telefone, situacao)
                    VALUES (:nome, :cargo, :email, :tel, :sit)
                ");
                $stmt->execute([
                    ':nome'  => $nome,
                    ':cargo' => $cargo,
                    ':email' => $email,
                    ':tel'   => $telefone,
                    ':sit'   => $situacao,
                ]);
                $_SESSION['msg']      = 'Funcionário cadastrado com sucesso!';
                $_SESSION['msg_tipo'] = 'success';
            }
            header('Location: listagem.php');
            exit;
        } catch (Exception $e) {
            $erros[] = 'Erro ao salvar: ' . $e->getMessage();
        }
    }

    // Em caso de erro, repopular campos
    $funcionario = [
        'id'       => $postId,
        'nome'     => $nome,
        'cargo'    => $cargo,
        'email'    => $email,
        'telefone' => $telefone,
        'situacao' => $situacao,
    ];
    $id = $postId;
}

$paginaAtiva = 'listagem';
$titulo = $id > 0 ? 'Editar Funcionário' : 'Novo Funcionário';

$cargos = ['Administrador', 'Gerente', 'Assistente', 'Analista', 'Desenvolvedor', 'Outro'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> — Cadastro de Funcionários</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="main-content" style="margin-top:68px;">
    <h2 class="page-title">Cadastro de Funcionários</h2>

    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger">
            <?php foreach ($erros as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-user-plus"></i> Cadastro de Funcionários
        </div>
        <div class="card-body">
            <form method="POST" action="cadastro.php<?= $id > 0 ? "?id=$id" : '' ?>" autocomplete="off">
                <input type="hidden" name="id" value="<?= $id ?>">

                <!-- Linha 1: ID + Nome/Cargo -->
                <div class="form-row">
                    <div class="form-group" style="max-width:140px;">
                        <label>ID <span class="auto-label">(Automático)</span></label>
                        <input type="text" class="form-control" value="<?= $id > 0 ? $id : '' ?>"
                               placeholder="Automático" disabled>
                    </div>
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" class="form-control" name="nome" placeholder="Nome"
                               value="<?= htmlspecialchars($funcionario['nome'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Cargo</label>
                        <select name="cargo" class="form-control" required>
                            <option value="">Cargo</option>
                            <?php foreach ($cargos as $c): ?>
                                <option value="<?= $c ?>" <?= ($funcionario['cargo'] ?? '') === $c ? 'selected' : '' ?>>
                                    <?= $c ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Linha 2: E-mail + E-mail confirmação -->
                <div class="form-row">
                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" class="form-control" name="email" placeholder="E-mail"
                               value="<?= htmlspecialchars($funcionario['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>E-mail (confirmação)</label>
                        <input type="email" class="form-control" name="email2" placeholder="Confirme o e-mail"
                               value="<?= htmlspecialchars($funcionario['email'] ?? '') ?>">
                    </div>
                </div>

                <!-- Linha 3: Telefone + Situação -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" class="form-control" name="telefone" placeholder="Telefone"
                               value="<?= htmlspecialchars($funcionario['telefone'] ?? '') ?>"
                               id="telefone">
                    </div>
                    <div class="form-group">
                        <label>Situação</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="situacao" value="Ativo"
                                    <?= ($funcionario['situacao'] ?? 'Ativo') === 'Ativo' ? 'checked' : '' ?>>
                                Ativo
                            </label>
                            <label>
                                <input type="radio" name="situacao" value="Inativo"
                                    <?= ($funcionario['situacao'] ?? '') === 'Inativo' ? 'checked' : '' ?>>
                                Inativo
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <button type="reset" class="btn btn-secondary" onclick="limparForm()">
                        <i class="fas fa-eraser"></i> Limpar
                    </button>
                    <a href="listagem.php" class="btn btn-warning">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="listagem.php" class="btn btn-danger">
                        <i class="fas fa-times"></i> Fechar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Máscara de telefone simples
document.getElementById('telefone').addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g,'');
    if (v.length <= 10) {
        v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    } else {
        v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    }
    e.target.value = v;
});

function limparForm() {
    setTimeout(() => {
        document.getElementById('telefone').value = '';
    }, 10);
}
</script>

</body>
</html>
