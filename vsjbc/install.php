<?php
/**
 * INSTALAÇÃO DO VSJBC
 * Execute este script UMA VEZ após fazer o upload e configurar config/database.php.
 * Acesse: https://www.engjoaopaulo.com/vsjbc/install.php
 * APAGUE este arquivo após a instalação!
 */

// Proteção simples por senha de instalação
$installPassword = 'vsjbc_install_2025';
if (!isset($_GET['key']) || $_GET['key'] !== $installPassword) {
    die('<h2>Acesso negado.</h2><p>Acesse com: install.php?key=' . htmlspecialchars($installPassword) . '</p>');
}

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/ai.php';

try {
    $cfg = require __DIR__ . '/config/database.php';
    // Conectar sem selecionar banco, para poder criá-lo se necessário
    $pdo = new PDO(
        "mysql:host={$cfg['host']};charset=utf8mb4",
        $cfg['user'], $cfg['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Criar banco se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$cfg['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$cfg['name']}`");

    // Executar schema
    $sql = file_get_contents(__DIR__ . '/sql/schema.sql');
    $pdo->exec($sql);

    // Criar usuário admin
    $adminEmail = 'admin@engjoaopaulo.com';
    $adminPass  = bin2hex(random_bytes(6)); // senha aleatória forte
    $adminHash  = password_hash($adminPass, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$adminEmail]);
    if (!$stmt->fetch()) {
        $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)')
            ->execute(['Administrador (João Paulo)', $adminEmail, $adminHash, 'admin']);
    }

    // Criar usuário gerente
    $managerEmail = 'chefe@engjoaopaulo.com';
    $managerPass  = bin2hex(random_bytes(6));
    $managerHash  = password_hash($managerPass, PASSWORD_BCRYPT);

    $stmt->execute([$managerEmail]);
    if (!$stmt->fetch()) {
        $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)')
            ->execute(['Gerente', $managerEmail, $managerHash, 'manager']);
    }

    echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">
    <title>Instalação VSJBC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head><body class="p-4">';

    echo '<div class="alert alert-success"><h4>✅ Instalação concluída com sucesso!</h4></div>';
    echo '<div class="card" style="max-width:500px"><div class="card-body">';
    echo '<h5 class="card-title">Credenciais de Acesso</h5>';
    echo '<table class="table table-sm">';
    echo "<tr><th>Admin</th><td>{$adminEmail}</td><td><code>{$adminPass}</code></td></tr>";
    echo "<tr><th>Gerente</th><td>{$managerEmail}</td><td><code>{$managerPass}</code></td></tr>";
    echo '</table>';
    echo '<p class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Anote essas senhas e APAGUE este arquivo (install.php) agora!</p>';
    echo '<a href="' . APP_URL . '/login" class="btn btn-primary">Ir para o Login →</a>';
    echo '</div></div>';

    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></script>';

} catch (Exception $e) {
    echo '<div style="color:red;font-family:monospace;padding:1rem">';
    echo '<h3>Erro na instalação:</h3>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<p>Verifique as configurações em config/database.php</p>';
    echo '</div>';
}
