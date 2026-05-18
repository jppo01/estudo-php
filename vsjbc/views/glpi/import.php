<?php $pageTitle = 'Importar GLPI'; ob_start(); ?>

<div class="row g-3">
    <div class="col-md-6">
        <div class="form-card">
            <h5 class="fw-bold mb-1"><i class="bi bi-upload me-2"></i>Importar Chamados GLPI</h5>
            <p class="text-muted small mb-4">Exporte os chamados do GLPI em CSV (separado por ponto e vírgula) e faça o upload aqui.</p>
            <?php if (!Auth::isAdmin()): ?>
            <div class="alert alert-warning small"><i class="bi bi-lock me-1"></i>Apenas administradores podem importar tickets.</div>
            <?php else: ?>
            <form method="POST" action="<?= base_url('glpi/importar') ?>" enctype="multipart/form-data">
                <?= CSRF::field() ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Arquivo CSV</label>
                    <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                    <div class="form-text">Máximo 5 MB. Formato: UTF-8, separador ;</div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-upload me-1"></i>Importar</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1"></i>Últimas Importações</h6>
            <?php if ($batches): ?>
            <table class="table table-sm small mb-0">
                <thead class="table-light"><tr><th>Arquivo</th><th>Tickets</th><th>Data</th></tr></thead>
                <tbody>
                <?php foreach ($batches as $b): ?>
                <tr>
                    <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= esc($b['import_batch']) ?>"><?= esc($b['import_batch']) ?></td>
                    <td><?= $b['total'] ?></td>
                    <td><?= datetime_br($b['imported_at']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?><p class="text-muted small mb-0">Nenhuma importação realizada ainda.</p><?php endif; ?>
        </div>
        <div class="form-card mt-3">
            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-1"></i>Colunas esperadas</h6>
            <div class="row small">
                <div class="col-6"><code>id</code>, <code>título</code>, <code>descrição</code>, <code>categoria</code></div>
                <div class="col-6"><code>status</code>, <code>prioridade</code>, <code>solicitante</code>, <code>técnico</code></div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$extraJs = '<script src="' . base_url('assets/js/oracle.js') . '"></script>';
require __DIR__ . '/../layouts/main.php';
