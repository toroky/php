<?php
// config/session.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

function getUsuarioNome() {
    return $_SESSION['usuario_nome'] ?? 'Usuário';
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit;
}
