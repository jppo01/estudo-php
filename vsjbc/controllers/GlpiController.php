<?php
class GlpiController
{
    private GlpiTicketModel $model;

    public function __construct()
    {
        $this->model = new GlpiTicketModel();
    }

    public function index(): void
    {
        Auth::require();
        $batches = $this->model->getLastBatches();
        $flash   = flash_get();
        require __DIR__ . '/../views/glpi/import.php';
    }

    public function import(): void
    {
        Auth::require();
        CSRF::requireValid();

        if (!Auth::isAdmin()) {
            flash('danger', 'Apenas administradores podem importar tickets GLPI.');
            Response::redirect('glpi');
        }

        $file = $_FILES['csv_file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            flash('danger', 'Erro no upload do arquivo.');
            Response::redirect('glpi');
        }

        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file['tmp_name']);
        $allowedMimes = ['text/plain','text/csv','application/csv','application/octet-stream'];

        if ($ext !== 'csv' || !in_array($mime, $allowedMimes, true)) {
            flash('danger', 'Apenas arquivos .csv são aceitos.');
            Response::redirect('glpi');
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            flash('danger', 'Arquivo muito grande (máximo 5 MB).');
            Response::redirect('glpi');
        }

        $rows   = $this->parseCsv($file['tmp_name']);
        $batch  = date('Y-m-d_H-i-s') . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
        $result = $this->model->importFromCsv($rows, $batch);

        flash('success',
            "Importação concluída: {$result['inserted']} inseridos, " .
            "{$result['updated']} atualizados, {$result['errors']} erros."
        );
        Response::redirect('glpi');
    }

    private function parseCsv(string $path): array
    {
        $rows    = [];
        $handle  = fopen($path, 'r');
        $headers = null;

        $map = [
            'id'            => 'glpi_id',
            'título'        => 'title',
            'title'         => 'title',
            'descrição'     => 'description',
            'description'   => 'description',
            'categoria'     => 'category',
            'category'      => 'category',
            'status'        => 'status',
            'état'          => 'status',
            'prioridade'    => 'priority',
            'priority'      => 'priority',
            'solicitante'   => 'requester',
            'requester'     => 'requester',
            'técnico'        => 'assignee',
            'assignee'      => 'assignee',
            'data abertura' => 'glpi_created_at',
            'opening date'  => 'glpi_created_at',
            'data'          => 'glpi_created_at',
            'solução'       => 'solution',
            'solution'      => 'solution',
        ];

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (!$headers) {
                $headers = array_map(fn($h) => strtolower(trim($h)), $row);
                continue;
            }
            $mapped = [];
            foreach ($headers as $i => $h) {
                $key = $map[$h] ?? null;
                if ($key) $mapped[$key] = trim($row[$i] ?? '');
            }
            if (!empty($mapped)) $rows[] = $mapped;
        }
        fclose($handle);
        return $rows;
    }
}
