<?php
ob_start();
?>
<div class="brand">
    <h1>VSJ<span>BC</span></h1>
    <p>Manutenção Assistencial &amp; Engenharia Clínica</p>
</div>

<?php if ($error = flash_get()): ?>
<div class="alert alert-<?= esc($error['type']) ?> py-2 small" role="alert">
    <?= $error['msg'] ?>
</div>
<?php endif; ?>

<form method="POST" action="<?= base_url('login') ?>">
    <?= CSRF::field() ?>
    <div class="mb-3">
        <label class="form-label small fw-semibold">E-mail</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" placeholder="seu@email.com" required autofocus>
        </div>
    </div>
    <div class="mb-4">
        <label class="form-label small fw-semibold">Senha</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
    </div>
    <button type="submit" class="btn btn-primary w-100 fw-semibold">
        <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
    </button>
</form>
<p class="text-center text-muted mt-3" style="font-size:.78rem">Acesso restrito — VSJBC &copy; <?= date('Y') ?></p>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth.php';
