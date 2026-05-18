<?php $pageTitle = 'Importar GLPI'; ob_start(); ?>

<div class="row g-3">
    <div class="col-md-6">
        <div class="form-card">
            <h5 class="fw-bold mb-1"><i class="bi bi-upload me-2"></i>Importar Chamados GLPI</h5>
            <p class="text-muted small mb-4">
                Exporte os chamados de manutenção do GLPI em formato CSV (separado por ponto e vírgula)
                e faça o upload aqui. Os dados ficam disponíveis para consulta no Oráculo IA.
            </p>

            <?php if (!Auth::isAdmin()): ?>
            <div class="alert alert-warning small">
                <i class="bi bi-lock me-1"></i>Apenas administradores podem importar tickets.
            </div>
            <?php else: ?>
            <form method="POST" action="<?= base_url('glpi/importar') ?>" enctype="multipart/form-data">
                <?= CSRF::field() ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Arquivo CSV</label>
                    <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                    <div class="form-text">Máximo 5 MB. Formato: UTF-8, separador ;</div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-cloud-upload me-1"></i>Importar
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1"></i>Últimas Importações</h6>
            <?php if ($batches): ?>
            <table class="table table-sm small mb-0">
                <thead class="table-light">
                    <tr><th>Arquivo</th><th>Tickets</th><th>Data</th></tr>
                </thead>
                <tbody>
                <?php foreach ($batches as $b): ?>
                <tr>
                    <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                        title="<?= esc($b['import_batch']) ?>">
                        <?= esc($b['import_batch']) ?>
                    </td>
                    <td><?= $b['total'] ?></td>
                    <td><?= datetime_br($b['imported_at']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-muted small mb-0">Nenhuma importação realizada ainda.</p>
            <?php endif; ?>
        </div>

        <div class="form-card mt-3">
            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-1"></i>Colunas esperadas no CSV</h6>
            <p class="text-muted small mb-2">O sistema reconhece automaticamente estas colunas:</p>
            <div class="row small">
                <div class="col-6">
                    <code>id</code> ou <code>ID</code><br>
                    <code>título</code> ou <code>title</code><br>
                    <code>descrição</code> ou <code>description</code><br>
                    <code>categoria</code> ou <code>category</code><br>
                </div>
                <div class="col-6">
                    <code>status</code><br>
                    <code>prioridade</code> ou <code>priority</code><br>
                    <code>solicitante</code> ou <code>requester</code><br>
                    <code>técnico</code> ou <code>assignee</code><br>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$extraJs = '<script src="' . base_url('assets/js/oracle.js') . '"></script>';
require __DIR__ . '/../layouts/main.php';
