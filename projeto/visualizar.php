<?php
// visualizar.php

require_once 'config/database.php';
require_once 'config/session.php';
requireLogin();

$pdo = getConnection();
$id  = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: listagem.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM funcionarios WHERE id = :id");
$stmt->execute([':id' => $id]);
$f = $stmt->fetch();

if (!$f) {
    header('Location: listagem.php');
    exit;
}

$paginaAtiva = 'listagem';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Funcionário</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .detail-row { display:flex; gap:18px; margin-bottom:12px; }
        .detail-item { flex:1; }
        .detail-item label { font-size:11px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:0.4px; display:block; margin-bottom:3px; }
        .detail-item span { font-size:14px; color:#222; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="main-content" style="margin-top:68px;">
    <h2 class="page-title">Visualizar Funcionário</h2>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-id-card"></i> Dados do Funcionário
        </div>
        <div class="card-body">
            <div class="detail-row">
                <div class="detail-item">
                    <label>ID</label>
                    <span><?= $f['id'] ?></span>
                </div>
                <div class="detail-item" style="flex:3">
                    <label>Nome</label>
                    <span><?= htmlspecialchars($f['nome']) ?></span>
                </div>
                <div class="detail-item" style="flex:2">
                    <label>Cargo</label>
                    <span><?= htmlspecialchars($f['cargo']) ?></span>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-item">
                    <label>E-mail</label>
                    <span><?= htmlspecialchars($f['email']) ?></span>
                </div>
                <div class="detail-item">
                    <label>Telefone</label>
                    <span><?= htmlspecialchars($f['telefone'] ?: '—') ?></span>
                </div>
                <div class="detail-item">
                    <label>Situação</label>
                    <span class="badge badge-<?= strtolower($f['situacao']) ?>">
                        <?= htmlspecialchars($f['situacao']) ?>
                    </span>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-item">
                    <label>Cadastrado em</label>
                    <span><?= date('d/m/Y H:i', strtotime($f['criado_em'])) ?></span>
                </div>
                <div class="detail-item">
                    <label>Atualizado em</label>
                    <span><?= date('d/m/Y H:i', strtotime($f['atualizado_em'])) ?></span>
                </div>
            </div>

            <div class="form-actions" style="margin-top:10px;">
                <a href="cadastro.php?id=<?= $f['id'] ?>" class="btn btn-warning">
                    <i class="fas fa-pencil-alt"></i> Editar
                </a>
                <a href="listagem.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
