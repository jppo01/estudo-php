<?php $pageTitle = 'Demanda #' . $demand['id']; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <a href="<?= base_url('demandas') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Imprimir</button>
        <a href="<?= base_url('demandas/' . $demand['id'] . '/editar') ?>" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i>Editar</a>
    </div>
</div>

<div class="print-title"><h2><?= esc($demand['title']) ?> <small class="text-muted">#<?= $demand['id'] ?></small></h2><hr></div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="form-card mb-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h5 class="fw-bold mb-0"><?= esc($demand['title']) ?></h5>
                <?= status_badge($demand['status']) ?>
            </div>
            <div class="row g-2 mb-3 small">
                <div class="col-6 col-md-3"><div class="text-muted">Prioridade</div><div><?= priority_badge($demand['priority']) ?></div></div>
                <div class="col-6 col-md-3"><div class="text-muted">Categoria</div><div><?= esc($demand['category'] ?? '—') ?></div></div>
                <div class="col-6 col-md-3"><div class="text-muted">Responsável</div><div><?= esc($demand['assignee'] ?? '—') ?></div></div>
                <div class="col-6 col-md-3"><div class="text-muted">Prazo</div><div><?= date_br($demand['deadline']) ?></div></div>
            </div>
            <?php if ($demand['description']): ?>
            <div class="mb-3"><div class="text-muted small mb-1">Descrição</div><div style="white-space:pre-wrap"><?= esc($demand['description']) ?></div></div>
            <?php endif; ?>
            <?php if ($demand['notes']): ?>
            <div class="alert alert-info py-2 small mb-0"><i class="bi bi-info-circle me-1"></i><strong>Notas:</strong> <?= esc($demand['notes']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-card mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-chat-left-text me-1"></i>Comentários</h6>
            <?php foreach ($comments as $c): ?>
            <div class="d-flex gap-2 mb-3">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                     style="width:32px;height:32px;min-width:32px;font-size:.8rem;font-weight:700">
                    <?= mb_strtoupper(mb_substr($c['user_name'], 0, 1)) ?>
                </div>
                <div><div class="fw-semibold small"><?= esc($c['user_name']) ?> <span class="text-muted fw-normal"><?= datetime_br($c['created_at']) ?></span></div>
                <div class="small" style="white-space:pre-wrap"><?= esc($c['comment']) ?></div></div>
            </div>
            <?php endforeach; ?>
            <?php if (!$comments): ?><p class="text-muted small mb-3">Nenhum comentário ainda.</p><?php endif; ?>
            <form method="POST" action="<?= base_url('demandas/' . $demand['id'] . '/comentar') ?>" class="no-print">
                <?= CSRF::field() ?>
                <div class="d-flex gap-2">
                    <textarea name="comment" class="form-control form-control-sm" rows="2" placeholder="Adicionar comentário..."></textarea>
                    <button type="submit" class="btn btn-primary btn-sm align-self-end"><i class="bi bi-send"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-card mb-3 no-print">
            <h6 class="fw-bold mb-3"><i class="bi bi-arrow-left-right me-1"></i>Mudar Status</h6>
            <form method="POST" action="<?= base_url('demandas/' . $demand['id'] . '/status') ?>">
                <?= CSRF::field() ?>
                <select name="status" class="form-select form-select-sm mb-2">
                    <?php foreach (['pendente','em_andamento','concluida','cancelada'] as $s): ?>
                    <option value="<?= $s ?>" <?= $demand['status'] === $s ? 'selected' : '' ?>><?= esc(status_label($s)) ?></option>
                    <?php endforeach; ?>
                </select>
                <textarea name="comment" class="form-control form-control-sm mb-2" rows="2" placeholder="Motivo (opcional)..."></textarea>
                <button type="submit" class="btn btn-primary btn-sm w-100">Atualizar Status</button>
            </form>
        </div>

        <div class="form-card mb-3">
            <h6 class="fw-bold mb-3">Informações</h6>
            <div class="small">
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Criado por</span><span><?= esc($demand['created_by_name']) ?></span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Criado em</span><span><?= datetime_br($demand['created_at']) ?></span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Atualizado</span><span><?= datetime_br($demand['updated_at']) ?></span></div>
            </div>
        </div>

        <?php if ($history): ?>
        <div class="form-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1"></i>Histórico</h6>
            <div class="timeline">
                <?php foreach ($history as $h): ?>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="small fw-semibold"><?= esc($h['user_name']) ?></div>
                        <div class="small text-muted"><?= esc($h['field_changed']) ?>
                            <?php if ($h['old_value'] && $h['new_value']): ?><?= esc($h['old_value']) ?> → <strong><?= esc($h['new_value']) ?></strong>
                            <?php elseif ($h['new_value']): ?><?= esc($h['new_value']) ?><?php endif; ?>
                        </div>
                        <?php if ($h['comment']): ?><div class="small text-secondary fst-italic">"<?= esc($h['comment']) ?>"</div><?php endif; ?>
                        <div class="activity-time"><?= datetime_br($h['changed_at']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$extraJs = '<script src="' . base_url('assets/js/oracle.js') . '"></script>';
require __DIR__ . '/../layouts/main.php';
