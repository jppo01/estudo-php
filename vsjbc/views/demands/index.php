<?php $pageTitle = 'Demandas'; ob_start(); ?>

<!-- Filtros -->
<form method="GET" class="table-card p-3 mb-3 no-print">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Buscar por título..." value="<?= esc($filters['search']) ?>">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">Todos os status</option>
                <?php foreach (['pendente','em_andamento','concluida','cancelada'] as $s): ?>
                <option value="<?= $s ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>>
                    <?= esc(status_label($s)) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="priority" class="form-select form-select-sm">
                <option value="">Todas as prioridades</option>
                <?php foreach (['baixa','media','alta','critica'] as $p): ?>
                <option value="<?= $p ?>" <?= $filters['priority'] === $p ? 'selected' : '' ?>>
                    <?= esc(priority_label($p)) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" class="form-control form-control-sm"
                   value="<?= esc($filters['date_from'] ?? '') ?>" placeholder="De">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" class="form-control form-control-sm"
                   value="<?= esc($filters['date_to'] ?? '') ?>" placeholder="Até">
        </div>
        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="bi bi-search"></i>
            </button>
            <a href="<?= base_url('demandas') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x"></i>
            </a>
        </div>
    </div>
</form>

<div class="table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><?= $total ?> demanda(s) encontrada(s)</span>
        <a href="<?= base_url('demandas/nova') ?>" class="btn btn-primary btn-sm no-print">
            <i class="bi bi-plus-lg me-1"></i>Nova Demanda
        </a>
    </div>

    <?php if ($demands): ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Categoria</th>
                    <th>Status</th>
                    <th>Prioridade</th>
                    <th>Responsável</th>
                    <th>Prazo</th>
                    <th class="no-print">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($demands as $d): ?>
            <tr>
                <td class="text-muted"><?= $d['id'] ?></td>
                <td>
                    <a href="<?= base_url('demandas/' . $d['id']) ?>" class="text-decoration-none fw-semibold">
                        <?= esc(truncate($d['title'], 50)) ?>
                    </a>
                </td>
                <td><?= esc($d['category'] ?? '—') ?></td>
                <td><?= status_badge($d['status']) ?></td>
                <td><?= priority_badge($d['priority']) ?></td>
                <td><?= esc($d['assignee'] ?? '—') ?></td>
                <td>
                    <?php if ($d['deadline']): ?>
                        <?php
                        $today   = new DateTime();
                        $due     = new DateTime($d['deadline']);
                        $late    = $due < $today && !in_array($d['status'], ['concluida','cancelada']);
                        ?>
                        <span class="<?= $late ? 'text-danger fw-semibold' : '' ?>">
                            <?= date_br($d['deadline']) ?>
                        </span>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td class="no-print">
                    <div class="d-flex gap-1">
                        <a href="<?= base_url('demandas/' . $d['id']) ?>" class="btn btn-outline-secondary btn-sm" title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?= base_url('demandas/' . $d['id'] . '/editar') ?>" class="btn btn-outline-primary btn-sm" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="<?= base_url('demandas/' . $d['id'] . '/excluir') ?>"
                              onsubmit="return confirm('Confirmar exclusão da demanda?')">
                            <?= CSRF::field() ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <?php
    $totalPages = ceil($total / $perPage);
    if ($totalPages > 1):
        $q = http_build_query(array_filter(array_merge($filters, ['page' => null])));
    ?>
    <div class="p-3 d-flex justify-content-center no-print">
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= $q ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="p-4 text-center text-muted">
        <i class="bi bi-inbox" style="font-size:2rem"></i>
        <p class="mt-2 mb-0">Nenhuma demanda encontrada.</p>
        <a href="<?= base_url('demandas/nova') ?>" class="btn btn-primary btn-sm mt-2">Criar primeira demanda</a>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$extraJs = '<script src="' . base_url('assets/js/oracle.js') . '"></script>';
require __DIR__ . '/../layouts/main.php';
