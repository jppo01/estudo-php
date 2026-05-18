<?php
class ReportController
{
    private DemandModel $model;

    public function __construct()
    {
        $this->model = new DemandModel();
    }

    public function index(): void
    {
        Auth::require();
        $filters = [
            'status'    => $_GET['status']    ?? '',
            'priority'  => $_GET['priority']  ?? '',
            'date_from' => Sanitizer::date($_GET['date_from'] ?? ''),
            'date_to'   => Sanitizer::date($_GET['date_to']   ?? ''),
            'search'    => Sanitizer::str('search', $_GET),
        ];
        $demands = $this->model->findAll($filters, 1, 500);
        $total   = count($demands);

        $byStatus   = [];
        $byPriority = [];
        foreach ($demands as $d) {
            $byStatus[$d['status']]     = ($byStatus[$d['status']]     ?? 0) + 1;
            $byPriority[$d['priority']] = ($byPriority[$d['priority']] ?? 0) + 1;
        }

        require __DIR__ . '/../views/reports/index.php';
    }

    public function exportCsv(): void
    {
        Auth::require();
        $filters = [
            'status'    => $_GET['status']    ?? '',
            'priority'  => $_GET['priority']  ?? '',
            'date_from' => Sanitizer::date($_GET['date_from'] ?? ''),
            'date_to'   => Sanitizer::date($_GET['date_to']   ?? ''),
        ];
        $demands = $this->model->findAll($filters, 1, 1000);

        $filename = 'demandas_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM para Excel

        fputcsv($out, ['ID','Título','Categoria','Status','Prioridade','Responsável','Prazo','Criado em'], ';');
        foreach ($demands as $d) {
            fputcsv($out, [
                $d['id'],
                $d['title'],
                $d['category']   ?? '',
                status_label($d['status']),
                priority_label($d['priority']),
                $d['assignee']   ?? '',
                $d['deadline']   ? date_br($d['deadline']) : '',
                datetime_br($d['created_at']),
            ], ';');
        }
        fclose($out);
        exit;
    }
}
