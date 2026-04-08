<?php
// listagem.php

require_once 'config/database.php';
require_once 'config/session.php';
requireLogin();

$pdo    = getConnection();
$busca  = trim($_GET['busca'] ?? '');
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$porPagina = 10;
$offset    = ($pagina - 1) * $porPagina;

// Contar total
if ($busca !== '') {
    $stmtCount = $pdo->prepare("
        SELECT COUNT(*) FROM funcionarios
        WHERE nome ILIKE :b OR cargo ILIKE :b OR email ILIKE :b
    ");
    $stmtCount->execute([':b' => "%$busca%"]);
} else {
    $stmtCount = $pdo->query("SELECT COUNT(*) FROM funcionarios");
}
$total     = (int)$stmtCount->fetchColumn();
$totalPags = (int)ceil($total / $porPagina);

// Buscar registros
if ($busca !== '') {
    $stmt = $pdo->prepare("
        SELECT * FROM funcionarios
        WHERE nome ILIKE :b OR cargo ILIKE :b OR email ILIKE :b
        ORDER BY id ASC
        LIMIT :lim OFFSET :off
    ");
    $stmt->bindValue(':b',   "%$busca%");
    $stmt->bindValue(':lim', $porPagina, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset,    PDO::PARAM_INT);
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("
        SELECT * FROM funcionarios
        ORDER BY id ASC
        LIMIT :lim OFFSET :off
    ");
    $stmt->bindValue(':lim', $porPagina, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset,    PDO::PARAM_INT);
    $stmt->execute();
}
$funcionarios = $stmt->fetchAll();

$paginaAtiva = 'listagem';

// Mensagem flash
$msg     = $_SESSION['msg']      ?? '';
$msgTipo = $_SESSION['msg_tipo'] ?? 'success';
unset($_SESSION['msg'], $_SESSION['msg_tipo']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Funcionários</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="main-content" style="margin-top:68px;">
    <h2 class="page-title">Cadastro de Funcionários</h2>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msgTipo ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-users"></i> Listagem de Funcionários
        </div>
        <div class="card-body">
            <!-- Toolbar -->
            <div class="list-toolbar">
                <form method="GET" action="listagem.php" style="flex:1;display:flex;gap:10px;">
                    <div class="search-box" style="flex:1;">
                        <span class="search-icon"><i class="fas fa-search"></i></span>
                        <input type="text" name="busca" placeholder="Buscar funcionário..."
                               value="<?= htmlspecialchars($busca) ?>">
                    </div>
                    <button type="submit" class="btn btn-info">Pesquisar</button>
                </form>
                <a href="cadastro.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Funcionário
                </a>
            </div>

            <!-- Tabela -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Cargo</th>
                            <th>E-mail</th>
                            <th>Situação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($funcionarios)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;color:#888;padding:24px;">
                                    Nenhum funcionário encontrado.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($funcionarios as $i => $f): ?>
                            <tr>
                                <td><?= $offset + $i + 1 ?>.</td>
                                <td><?= htmlspecialchars($f['nome']) ?></td>
                                <td><?= htmlspecialchars($f['cargo']) ?></td>
                                <td><?= htmlspecialchars($f['email']) ?></td>
                                <td>
                                    <span class="badge badge-<?= strtolower($f['situacao']) ?>">
                                        <?= htmlspecialchars($f['situacao']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="acoes">
                                        <a href="cadastro.php?id=<?= $f['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="visualizar.php?id=<?= $f['id'] ?>" class="btn btn-info btn-sm" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button onclick="confirmarExclusao(<?= $f['id'] ?>, '<?= htmlspecialchars(addslashes($f['nome'])) ?>')"
                                                class="btn btn-danger btn-sm" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <?php if ($totalPags > 1): ?>
            <div class="pagination">
                <?php if ($pagina > 1): ?>
                    <a href="?pagina=<?= $pagina-1 ?>&busca=<?= urlencode($busca) ?>">« Anterior</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPags; $p++): ?>
                    <?php if ($p === $pagina): ?>
                        <span class="active"><?= $p ?></span>
                    <?php else: ?>
                        <a href="?pagina=<?= $p ?>&busca=<?= urlencode($busca) ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagina < $totalPags): ?>
                    <a href="?pagina=<?= $pagina+1 ?>&busca=<?= urlencode($busca) ?>">Próximo »</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal-overlay" id="modalExcluir">
    <div class="modal-box">
        <h3><i class="fas fa-exclamation-triangle" style="color:#e74c3c"></i> Confirmar Exclusão</h3>
        <p id="modalMsg">Deseja excluir este funcionário?</p>
        <div class="modal-actions">
            <a href="#" id="btnConfirmarExcluir" class="btn btn-danger">Excluir</a>
            <button onclick="fecharModal()" class="btn btn-secondary">Cancelar</button>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(id, nome) {
    document.getElementById('modalMsg').textContent = 'Deseja excluir o funcionário "' + nome + '"?';
    document.getElementById('btnConfirmarExcluir').href = 'excluir.php?id=' + id;
    document.getElementById('modalExcluir').classList.add('open');
}
function fecharModal() {
    document.getElementById('modalExcluir').classList.remove('open');
}
document.getElementById('modalExcluir').addEventListener('click', function(e) {
    if (e.target === this) fecharModal();
});
</script>

</body>
</html>
