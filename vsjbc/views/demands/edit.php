<?php $pageTitle = 'Editar Demanda #' . $demand['id']; ob_start(); ?>

<div class="form-card" style="max-width:700px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0"><i class="bi bi-pencil me-2"></i>Editar Demanda #<?= $demand['id'] ?></h5>
        <a href="<?= base_url('demandas/' . $demand['id']) ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <?php require __DIR__ . '/_form.php'; ?>
</div>

<?php
$content = ob_get_clean();
$extraJs = '<script src="' . base_url('assets/js/oracle.js') . '"></script>';
require __DIR__ . '/../layouts/main.php';
