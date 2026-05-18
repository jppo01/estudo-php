<?php $pageTitle = 'Dashboard'; ob_start(); ?>

<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['label'=>'Pendentes',    'key'=>'pendente',     'cls'=>'pendente',  'icon'=>'clock'],
        ['label'=>'Em Andamento', 'key'=>'em_andamento', 'cls'=>'andamento', 'icon'=>'arrow-repeat'],
        ['label'=>'Concluídas',   'key'=>'concluida',    'cls'=>'concluida', 'icon'=>'check-circle'],
        ['label'=>'Canceladas',   'key'=>'cancelada',    'cls'=>'cancelada', 'icon'=>'x-circle'],
    ];
    foreach ($cards as $c):
        $n = $stats['by_status'][$c['key']] ?? 0;
    ?>
    <div class="col-6 col-md-3">
        <div class="stat-card border-<?= $c['cls'] ?>">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="number"><?= $n ?></div>
                    <div class="label"><?= $c['label'] ?></div>
                </div>
                <i class="bi bi-<?= $c['icon'] ?>" style="font-size:1.5rem;opacity:.25"></i>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="row g-3">
            <div class="col-6 col-md-12">
                <div class="stat-card" style="border-left:4px solid #8b5cf6">
                    <div class="number"><?= $stats['total'] ?></div>
                    <div class="label">Total de Demandas</div>
                </div>
            </div>
            <div class="col-6 col-md-12">
                <div class="stat-card" style="border-left:4px solid #ef4444">
                    <div class="number"><?= $stats['due_soon'] ?></div>
                    <div class="label">Prazo nos próximos 7 dias</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card h-100">
            <div class="card-header">Prioridade</div>
            <div class="p-3"><canvas id="chartPriority" height="160"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card h-100">
            <div class="card-header">Status</div>
            <div class="p-3"><canvas id="chartStatus" height="160"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <?php if ($dueSoon): ?>
    <div class="col-md-5">
        <div class="table-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-alarm text-danger"></i> Prazos Próximos
            </div>
            <div class="p-3">
                <?php foreach ($dueSoon as $d): ?>
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                    <a href="<?= base_url('demandas/' . $d['id']) ?>" class="text-decoration-none text-dark small">
                        <?= esc(truncate($d['title'], 40)) ?>
                    </a>
                    <span class="badge bg-danger ms-2"><?= date_br($d['deadline']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="col-md-<?= $dueSoon ? '7' : '12' ?>">
        <div class="table-card">
            <div class="card-header"><i class="bi bi-activity me-1"></i>Atividade Recente</div>
            <div class="p-3">
                <?php if ($activity): ?>
                <?php foreach ($activity as $a): ?>
                <div class="activity-item">
                    <div class="d-flex justify-content-between">
                        <span>
                            <strong><?= esc($a['user_name']) ?></strong> alterou
                            <em><?= esc($a['field_changed']) ?></em> em
                            <a href="<?= base_url('demandas/' . $a['demand_id']) ?>" class="text-decoration-none">
                                <?= esc(truncate($a['demand_title'], 35)) ?>
                            </a>
                            <?php if ($a['new_value']): ?>
                            → <strong><?= esc($a['new_value']) ?></strong>
                            <?php endif; ?>
                        </span>
                        <span class="activity-time ms-2 text-nowrap"><?= datetime_br($a['changed_at']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="text-muted small mb-0">Nenhuma atividade registrada ainda.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const statsData = <?= json_encode($stats, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
</script>
<?php
$content  = ob_get_clean();
$extraJs  = '<script src="' . base_url('assets/js/dashboard.js') . '"></script><script src="' . base_url('assets/js/oracle.js') . '"></script>';
require __DIR__ . '/../layouts/main.php';
