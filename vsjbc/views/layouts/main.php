<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle ?? 'VSJBC') ?> — <?= APP_NAME ?></title>
    <!-- PWA -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="theme-color" content="#1a56db">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="VSJBC Man. Assistencial">
    <link rel="apple-touch-icon" href="<?= base_url('assets/img/icon-192.php') ?>">
    <!-- /PWA -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
</head>
<body>
<div class="app-wrapper">

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <a href="<?= base_url('dashboard') ?>" class="sidebar-brand">
            <i class="bi bi-layers-fill me-2"></i>VSJ<span>BC</span>
        </a>
        <div class="sidebar-nav">
            <span class="sidebar-section">Principal</span>
            <a href="<?= base_url('dashboard') ?>" class="nav-link <?= active_link('dashboard') ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <span class="sidebar-section">Demandas</span>
            <a href="<?= base_url('demandas') ?>" class="nav-link <?= active_link('demandas') ?>">
                <i class="bi bi-list-task"></i> Listar Demandas
            </a>
            <a href="<?= base_url('demandas/nova') ?>" class="nav-link <?= active_link('demandas/nova') ?>">
                <i class="bi bi-plus-circle"></i> Nova Demanda
            </a>

            <span class="sidebar-section">Análise</span>
            <a href="<?= base_url('relatorios') ?>" class="nav-link <?= active_link('relatorios') ?>">
                <i class="bi bi-bar-chart-line"></i> Relatórios
            </a>

            <span class="sidebar-section">Integrações</span>
            <a href="<?= base_url('glpi') ?>" class="nav-link <?= active_link('glpi') ?>">
                <i class="bi bi-upload"></i> Importar GLPI
            </a>
            <a href="<?= base_url('oraculo') ?>" class="nav-link <?= active_link('oraculo') ?>">
                <i class="bi bi-robot"></i> Oráculo (IA)
            </a>
        </div>

        <!-- Usuário logado -->
        <div style="position:absolute;bottom:0;width:100%;padding:1rem 1.5rem;border-top:1px solid rgba(255,255,255,.08)">
            <div style="color:#c8d3e0;font-size:.85rem">
                <i class="bi bi-person-circle me-1"></i>
                <?= esc(Auth::user()['name']) ?>
                <span class="ms-1 badge bg-secondary" style="font-size:.65rem"><?= esc(Auth::user()['role']) ?></span>
            </div>
            <a href="<?= base_url('logout') ?>" style="color:#f87171;font-size:.82rem;text-decoration:none">
                <i class="bi bi-box-arrow-left me-1"></i>Sair
            </a>
        </div>
    </nav>

    <!-- Main -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="document.getElementById('sidebar').classList.toggle('open')">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="page-title"><?= esc($pageTitle ?? '') ?></h1>
            </div>
            <div class="text-muted" style="font-size:.83rem">
                <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?>
            </div>
        </div>

        <!-- Flash -->
        <?php $flash = $flash ?? flash_get(); if ($flash): ?>
        <div class="alert alert-<?= esc($flash['type']) ?> alert-dismissible fade show mx-3 mt-3 mb-0" role="alert">
            <?= $flash['msg'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Conteúdo da página -->
        <div class="p-3 p-md-4">
            <?= $content ?? '' ?>
        </div>
    </div>

</div>

<!-- Oracle Chat Widget -->
<?php require __DIR__ . '/../partials/_oracle_chat.php'; ?>

<script>window.APP_URL = '<?= APP_URL ?>'; window.CSRF_TOKEN = '<?= CSRF::token() ?>';</script>
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/vsjbc/sw.js', { scope: '/vsjbc/' });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?php if (isset($extraJs)): echo $extraJs; endif; ?>
</body>
</html>
