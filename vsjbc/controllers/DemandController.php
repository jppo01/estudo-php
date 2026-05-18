<?php
class DemandController
{
    private DemandModel $model;
    private DemandCommentModel $comments;

    private const STATUSES   = ['pendente','em_andamento','concluida','cancelada'];
    private const PRIORITIES = ['baixa','media','alta','critica'];

    public function __construct()
    {
        $this->model    = new DemandModel();
        $this->comments = new DemandCommentModel();
    }

    public function index(): void
    {
        Auth::require();
        $filters = [
            'status'    => Sanitizer::inList($_GET['status']   ?? '', self::STATUSES),
            'priority'  => Sanitizer::inList($_GET['priority'] ?? '', self::PRIORITIES),
            'search'    => Sanitizer::str('search', $_GET),
            'date_from' => Sanitizer::date($_GET['date_from'] ?? ''),
            'date_to'   => Sanitizer::date($_GET['date_to']   ?? ''),
        ];
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $demands  = $this->model->findAll($filters, $page);
        $total    = $this->model->count($filters);
        $perPage  = 20;
        $flash    = flash_get();
        require __DIR__ . '/../views/demands/index.php';
    }

    public function create(): void
    {
        Auth::require();
        $flash = flash_get();
        require __DIR__ . '/../views/demands/create.php';
    }

    public function store(): void
    {
        Auth::require();
        CSRF::requireValid();

        $data = $this->extractFormData();
        $errors = $this->validate($data);

        if ($errors) {
            flash('danger', implode('<br>', $errors));
            Response::redirect('demandas/nova');
        }

        $id = $this->model->create($data, Auth::id());
        flash('success', 'Demanda criada com sucesso!');
        Response::redirect("demandas/{$id}");
    }

    public function show(string $id): void
    {
        Auth::require();
        $demand   = $this->model->findById((int)$id);
        if (!$demand) Response::notFound();
        $history  = (new DemandHistoryModel())->getByDemand((int)$id);
        $comments = $this->comments->getByDemand((int)$id);
        $flash    = flash_get();
        require __DIR__ . '/../views/demands/view.php';
    }

    public function edit(string $id): void
    {
        Auth::require();
        $demand = $this->model->findById((int)$id);
        if (!$demand) Response::notFound();
        $flash = flash_get();
        require __DIR__ . '/../views/demands/edit.php';
    }

    public function update(string $id): void
    {
        Auth::require();
        CSRF::requireValid();

        $demand = $this->model->findById((int)$id);
        if (!$demand) Response::notFound();

        $data   = $this->extractFormData();
        $errors = $this->validate($data);

        if ($errors) {
            flash('danger', implode('<br>', $errors));
            Response::redirect("demandas/{$id}/editar");
        }

        $this->model->update((int)$id, $data, Auth::id());
        flash('success', 'Demanda atualizada com sucesso!');
        Response::redirect("demandas/{$id}");
    }

    public function delete(string $id): void
    {
        Auth::require();
        CSRF::requireValid();
        $this->model->softDelete((int)$id, Auth::id());
        flash('success', 'Demanda excluída.');
        Response::redirect('demandas');
    }

    public function changeStatus(string $id): void
    {
        Auth::require();
        CSRF::requireValid();
        $status  = Sanitizer::inList(Sanitizer::str('status', $_POST), self::STATUSES);
        $comment = Sanitizer::str('comment', $_POST);
        if ($status) {
            $this->model->changeStatus((int)$id, $status, Auth::id(), $comment);
            flash('success', 'Status atualizado.');
        }
        Response::redirect("demandas/{$id}");
    }

    public function addComment(string $id): void
    {
        Auth::require();
        CSRF::requireValid();
        $comment = Sanitizer::str('comment', $_POST);
        if ($comment) {
            $this->comments->add((int)$id, Auth::id(), $comment);
        }
        Response::redirect("demandas/{$id}");
    }

    public function ajaxStatus(string $id = ''): void
    {
        Auth::require();
        if (!CSRF::verify()) {
            Response::json(['error' => 'CSRF inválido'], 403);
        }
        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $status = Sanitizer::inList($body['status'] ?? '', self::STATUSES);
        if (!$status) Response::json(['error' => 'Status inválido'], 400);
        $idInt = (int)($body['id'] ?? 0);
        $this->model->changeStatus($idInt, $status, Auth::id());
        Response::json(['ok' => true]);
    }

    private function extractFormData(): array
    {
        return [
            'title'       => Sanitizer::str('title'),
            'description' => Sanitizer::str('description'),
            'category'    => Sanitizer::str('category'),
            'priority'    => Sanitizer::inList(Sanitizer::str('priority'), self::PRIORITIES, 'media'),
            'status'      => Sanitizer::inList(Sanitizer::str('status'),   self::STATUSES,   'pendente'),
            'deadline'    => Sanitizer::date($_POST['deadline'] ?? ''),
            'assignee'    => Sanitizer::str('assignee'),
            'notes'       => Sanitizer::str('notes'),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (strlen($data['title']) < 3) $errors[] = 'O título deve ter pelo menos 3 caracteres.';
        if (strlen($data['title']) > 200) $errors[] = 'O título deve ter no máximo 200 caracteres.';
        return $errors;
    }
}
