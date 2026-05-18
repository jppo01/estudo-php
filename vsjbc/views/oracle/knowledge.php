<?php $pageTitle = 'Oráculo — Base de Conhecimento'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted small mb-0">Cadastre as perguntas e respostas que o Oráculo usará para responder.</p>
    <a href="<?= base_url('oraculo/novo') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Adicionar</a>
</div>

<div class="table-card">
    <div class="card-header fw-bold"><?= count($entries) ?> entradas cadastradas</div>
    <?php if ($entries): ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Categoria</th><th>Pergunta</th><th>Tags</th><th>Ativo</th><th>Ações</th></tr>
            </thead>
            <tbody>
            <?php foreach ($entries as $e): ?>
            <tr>
                <td><?= $e['id'] ?></td>
                <td><?= esc($e['category'] ?? '—') ?></td>
                <td><?= esc(truncate($e['question'], 60)) ?></td>
                <td><?php foreach (array_filter(explode(',', $e['tags'] ?? '')) as $tag): ?>
                    <span class="badge bg-secondary"><?= esc(trim($tag)) ?></span>
                <?php endforeach; ?></td>
                <td><?= $e['active'] ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>' ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="<?= base_url('oraculo/' . $e['id'] . '/editar') ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="<?= base_url('oraculo/' . $e['id'] . '/excluir') ?>" onsubmit="return confirm('Remover?')">
                            <?= CSRF::field() ?>
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="p-4 text-center text-muted">
        <i class="bi bi-robot" style="font-size:2rem"></i>
        <p class="mt-2 mb-2">O Oráculo ainda não tem conhecimento cadastrado.</p>
        <a href="<?= base_url('oraculo/novo') ?>" class="btn btn-primary btn-sm">Adicionar primeiro conhecimento</a>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$extraJs = '<script src="' . base_url('assets/js/oracle.js') . '"></script>';
require __DIR__ . '/../layouts/main.php';
