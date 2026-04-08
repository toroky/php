<?php
// includes/navbar.php
// Requer que $paginaAtiva seja definida antes de incluir
$paginaAtiva = $paginaAtiva ?? '';
?>
<nav class="navbar">
    <a href="listagem.php" class="navbar-brand">
        <span class="brand-icon">🌐</span>
        <span>Cadastro de Funcionários</span>
    </a>
    <ul class="navbar-nav">
        <li><a href="listagem.php" <?= $paginaAtiva === 'inicio' ? 'class="active"' : '' ?>>Início</a></li>
        <li><a href="listagem.php" <?= $paginaAtiva === 'listagem' ? 'class="active"' : '' ?>>Listagem</a></li>
    </ul>
    <div class="navbar-user">
        Olá, <?= htmlspecialchars(getUsuarioNome()) ?> ▾
        <div class="dropdown-menu">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
    </div>
</nav>
