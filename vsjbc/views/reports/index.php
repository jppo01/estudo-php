<?php $pageTitle = 'Relatórios'; ob_start(); ?>

<!-- Filtros -->
<form method="GET" class="table-card p-3 mb-3 no-print">
    <div class="row g-2 align-items-end">
        <div class="col-md-2">
            <label class="form-label small fw-semibold mb-1">De</label>
            <input type="date" name="date_from" class="form-control form-control-sm"
                   value="<?= esc($filters['date_from'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold mb-1">Até</label>
            <input type="date" name="date_to" class="form-control form-control-sm"
                   value="<?= esc($filters['date_to'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Todos</option>
                <?php foreach (['pendente','em_andamento','concluida','cancelada'] as $s): ?>
                <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>>
                    <?= esc(status_label($s)) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold mb-1">Prioridade</label>
            <select name="priority" class="form-select form-select-sm">
                <option value="">Todas</option>
                <?php foreach (['baixa','media','alta','critica'] as $p): ?>
                <option value="<?= $p ?>" <?= ($filters['priority'] ?? '') === $p ? 'selected' : '' ?>>
                    <?= esc(priority_label($p)) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold mb-1">Buscar</label>
            <input type="text" name="search" class="form-control form-control-sm"
                   value="<?= esc($filters['search'] ?? '') ?>" placeholder="Título...">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="bi bi-funnel"></i>
            </button>
        </div>
    </div>
</form>

<!-- Cabeçalho de impressão -->
<div class="print-title mb-3">
    <h2><?= APP_NAME ?> — Relatório de Demandas</h2>
    <p>Gerado em: <?= datetime_br(date('Y-m-d H:i:s')) ?></p>
    <hr>
</div>

<!-- Resumo -->
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="table-card">
            <div class="card-header fw-bold">Resumo por Status</div>
            <table class="table table-sm mb-0 small">
                <thead class="table-light"><tr><th>Status</th><th class="text-end">Qtd</th></tr></thead>
                <tbody>
                <?php foreach ($byStatus as $s => $n): ?>
                <tr><td><?= status_badge($s) ?></td><td class="text-end fw-semibold"><?= $n ?></td></tr>
                <?php endforeach; ?>
                <tr class="fw-bold table-light"><td>Total</td><td class="text-end"><?= $total ?></td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="table-card">
            <div class="card-header fw-bold">Resumo por Prioridade</div>
            <table class="table table-sm mb-0 small">
                <thead class="table-light"><tr><th>Prioridade</th><th class="text-end">Qtd</th></tr></thead>
                <tbody>
                <?php foreach ($byPriority as $p => $n): ?>
                <tr><td><?= priority_badge($p) ?></td><td class="text-end fw-semibold"><?= $n ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ações de exportação -->
<div class="d-flex gap-2 mb-3 no-print">
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-printer me-1"></i>Imprimir
    </button>
    <a href="<?= base_url('relatorios/exportar') ?>?<?= http_build_query(array_filter($filters)) ?>"
       class="btn btn-outline-success btn-sm">
        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar CSV
    </a>
</div>

<!-- Lista detalhada -->
<div class="table-card">
    <div class="card-header fw-bold">
        Demandas (<?= $total ?>)
    </div>
    <?php if ($demands): ?>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Título</th><th>Cat.</th>
                    <th>Status</th><th>Prioridade</th>
                    <th>Responsável</th><th>Prazo</th><th>Criado</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($demands as $d): ?>
            <tr>
                <td><?= $d['id'] ?></td>
                <td><?= esc(truncate($d['title'], 45)) ?></td>
                <td><?= esc($d['category'] ?? '—') ?></td>
                <td><?= status_badge($d['status']) ?></td>
                <td><?= priority_badge($d['priority']) ?></td>
                <td><?= esc($d['assignee'] ?? '—') ?></td>
                <td><?= date_br($d['deadline']) ?></td>
                <td><?= date_br($d['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="p-4 text-center text-muted small">Nenhum resultado com os filtros selecionados.</div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$extraJs = '<script src="' . base_url('assets/js/oracle.js') . '"></script>';
require __DIR__ . '/../layouts/main.php';
