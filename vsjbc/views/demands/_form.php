<?php
$isEdit  = isset($demand);
$action  = $isEdit
    ? base_url('demandas/' . $demand['id'] . '/editar')
    : base_url('demandas/nova');
$d = $demand ?? [];
?>
<form method="POST" action="<?= $action ?>">
    <?= CSRF::field() ?>

    <div class="mb-3">
        <label class="form-label fw-semibold small">Título <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control"
               value="<?= esc($d['title'] ?? '') ?>" required maxlength="200"
               placeholder="Descreva a demanda de forma objetiva">
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label fw-semibold small">Status</label>
            <select name="status" class="form-select">
                <?php foreach (['pendente','em_andamento','concluida','cancelada'] as $s): ?>
                <option value="<?= $s ?>" <?= ($d['status'] ?? 'pendente') === $s ? 'selected' : '' ?>>
                    <?= esc(status_label($s)) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold small">Prioridade</label>
            <select name="priority" class="form-select">
                <?php foreach (['baixa','media','alta','critica'] as $p): ?>
                <option value="<?= $p ?>" <?= ($d['priority'] ?? 'media') === $p ? 'selected' : '' ?>>
                    <?= esc(priority_label($p)) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold small">Prazo</label>
            <input type="date" name="deadline" class="form-control"
                   value="<?= esc($d['deadline'] ?? '') ?>">
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold small">Categoria</label>
            <input type="text" name="category" class="form-control"
                   value="<?= esc($d['category'] ?? '') ?>" placeholder="Ex: Infraestrutura, TI...">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold small">Responsável</label>
            <input type="text" name="assignee" class="form-control"
                   value="<?= esc($d['assignee'] ?? '') ?>" placeholder="Nome do técnico">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold small">Descrição</label>
        <textarea name="description" class="form-control" rows="4"
                  placeholder="Detalhes da demanda..."><?= esc($d['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold small">Notas Internas</label>
        <textarea name="notes" class="form-control" rows="2"
                  placeholder="Observações internas..."><?= esc($d['notes'] ?? '') ?></textarea>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Salvar Alterações' : 'Criar Demanda' ?>
        </button>
        <a href="<?= base_url('demandas') ?>" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
