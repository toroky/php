<?php
// excluir.php

require_once 'config/database.php';
require_once 'config/session.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        $pdo  = getConnection();
        $stmt = $pdo->prepare("DELETE FROM funcionarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['msg']      = 'Funcionário excluído com sucesso.';
        $_SESSION['msg_tipo'] = 'success';
    } catch (Exception $e) {
        $_SESSION['msg']      = 'Erro ao excluir: ' . $e->getMessage();
        $_SESSION['msg_tipo'] = 'danger';
    }
}

header('Location: listagem.php');
exit;
