<?php $pageTitle = 'Nova Demanda'; ob_start(); ?>

<div class="form-card" style="max-width:700px">
    <h5 class="fw-bold mb-4"><i class="bi bi-plus-circle me-2"></i>Nova Demanda</h5>
    <?php require __DIR__ . '/_form.php'; ?>
</div>

<?php
$content = ob_get_clean();
$extraJs = '<script src="' . base_url('assets/js/oracle.js') . '"></script>';
require __DIR__ . '/../layouts/main.php';
