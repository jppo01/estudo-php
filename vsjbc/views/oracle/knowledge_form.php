<?php
$isEdit    = isset($entry);
$pageTitle = $isEdit ? 'Editar Conhecimento' : 'Novo Conhecimento';
$action    = $isEdit
    ? base_url('oraculo/' . $entry['id'] . '/editar')
    : base_url('oraculo/novo');
ob_start();
?>

<div class="form-card" style="max-width:700px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-robot me-2"></i><?= $pageTitle ?>
        </h5>
        <a href="<?= base_url('oraculo') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>

    <form method="POST" action="<?= $action ?>">
        <?= CSRF::field() ?>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Categoria</label>
                <input type="text" name="category" class="form-control"
                       value="<?= esc($entry['category'] ?? '') ?>"
                       placeholder="Ex: Equipamentos, Manutenção Preventiva, GLPI, Normas...">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Tags (separadas por vírgula)</label>
                <input type="text" name="tags" class="form-control"
                       value="<?= esc($entry['tags'] ?? '') ?>"
                       placeholder="servidor, rede, backup...">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">Pergunta / Tópico <span class="text-danger">*</span></label>
            <input type="text" name="question" class="form-control"
                   value="<?= esc($entry['question'] ?? '') ?>"
                   placeholder="Como funciona o processo de backup?" required>
            <div class="form-text">Use palavras-chave que facilitam a busca pelo Oráculo.</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">Resposta / Contexto <span class="text-danger">*</span></label>
            <textarea name="answer" class="form-control" rows="6"
                      placeholder="Forneça a resposta ou contexto que o Oráculo deve usar..." required><?= esc($entry['answer'] ?? '') ?></textarea>
        </div>

        <div class="mb-4 form-check">
            <input type="checkbox" name="active" id="active" class="form-check-input"
                   <?= ($entry['active'] ?? 1) ? 'checked' : '' ?>>
            <label class="form-check-label small fw-semibold" for="active">
                Ativo (o Oráculo usa esta entrada nas respostas)
            </label>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Salvar' : 'Adicionar ao Oráculo' ?>
            </button>
            <a href="<?= base_url('oraculo') ?>" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$extraJs = '<script src="' . base_url('assets/js/oracle.js') . '"></script>';
require __DIR__ . '/../layouts/main.php';
