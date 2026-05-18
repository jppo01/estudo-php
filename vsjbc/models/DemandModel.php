<?php
class DemandModel
{
    private DemandHistoryModel $history;

    public function __construct()
    {
        $this->history = new DemandHistoryModel();
    }

    // ── Leitura ──────────────────────────────────────────────────────────────

    public function findAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        [$where, $params] = $this->buildWhere($filters);
        $offset = ($page - 1) * $perPage;
        return Database::fetchAll(
            "SELECT d.*, u.name AS created_by_name
             FROM demands d
             JOIN users u ON u.id = d.created_by
             {$where}
             ORDER BY d.created_at DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );
    }

    public function count(array $filters = []): int
    {
        [$where, $params] = $this->buildWhere($filters);
        $row = Database::fetchOne("SELECT COUNT(*) AS n FROM demands d {$where}", $params);
        return (int)($row['n'] ?? 0);
    }

    public function findById(int $id): ?array
    {
        return Database::fetchOne(
            'SELECT d.*, u.name AS created_by_name
             FROM demands d
             JOIN users u ON u.id = d.created_by
             WHERE d.id = ? AND d.deleted_at IS NULL',
            [$id]
        );
    }

    public function getStats(): array
    {
        $byStatus = Database::fetchAll(
            'SELECT status, COUNT(*) AS total FROM demands WHERE deleted_at IS NULL GROUP BY status'
        );
        $byPriority = Database::fetchAll(
            'SELECT priority, COUNT(*) AS total FROM demands WHERE deleted_at IS NULL GROUP BY priority'
        );
        $total = Database::fetchOne('SELECT COUNT(*) AS n FROM demands WHERE deleted_at IS NULL');
        $dueSoon = Database::fetchOne(
            "SELECT COUNT(*) AS n FROM demands
             WHERE deleted_at IS NULL AND status NOT IN ('concluida','cancelada')
             AND deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)"
        );

        $statusMap = array_column($byStatus, 'total', 'status');
        $priorityMap = array_column($byPriority, 'total', 'priority');

        return [
            'total'         => (int)($total['n'] ?? 0),
            'due_soon'      => (int)($dueSoon['n'] ?? 0),
            'by_status'     => $statusMap,
            'by_priority'   => $priorityMap,
        ];
    }

    public function getRecentActivity(int $limit = 10): array
    {
        return Database::fetchAll(
            'SELECT h.changed_at, h.field_changed, h.old_value, h.new_value, h.comment,
                    u.name AS user_name, d.title AS demand_title, d.id AS demand_id
             FROM demand_history h
             JOIN users u ON u.id = h.changed_by
             JOIN demands d ON d.id = h.demand_id
             ORDER BY h.changed_at DESC
             LIMIT ?',
            [$limit]
        );
    }

    public function getDueSoon(int $days = 7): array
    {
        return Database::fetchAll(
            "SELECT * FROM demands
             WHERE deleted_at IS NULL AND status NOT IN ('concluida','cancelada')
             AND deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
             ORDER BY deadline ASC",
            [$days]
        );
    }

    public function searchForContext(string $query, int $limit = 5): array
    {
        $like = '%' . $query . '%';
        return Database::fetchAll(
            "SELECT id, title, description, status, priority, deadline, assignee
             FROM demands
             WHERE deleted_at IS NULL AND (title LIKE ? OR description LIKE ?)
             ORDER BY updated_at DESC LIMIT ?",
            [$like, $like, $limit]
        );
    }

    // ── Escrita ──────────────────────────────────────────────────────────────

    public function create(array $data, int $userId): int
    {
        Database::query(
            'INSERT INTO demands (title, description, category, priority, status, deadline, assignee, notes, created_by)
             VALUES (:title,:description,:category,:priority,:status,:deadline,:assignee,:notes,:created_by)',
            [
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'category'    => $data['category']    ?? null,
                'priority'    => $data['priority']    ?? 'media',
                'status'      => $data['status']      ?? 'pendente',
                'deadline'    => $data['deadline']    ?? null,
                'assignee'    => $data['assignee']    ?? null,
                'notes'       => $data['notes']       ?? null,
                'created_by'  => $userId,
            ]
        );
        $id = (int)Database::lastInsertId();
        $this->history->record($id, 'criação', null, 'Demanda criada', $userId);
        return $id;
    }

    public function update(int $id, array $data, int $userId): bool
    {
        $current = $this->findById($id);
        if (!$current) return false;

        Database::query(
            'UPDATE demands SET title=:title, description=:description, category=:category,
             priority=:priority, status=:status, deadline=:deadline, assignee=:assignee, notes=:notes
             WHERE id=:id',
            [
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'category'    => $data['category']    ?? null,
                'priority'    => $data['priority'],
                'status'      => $data['status'],
                'deadline'    => $data['deadline']    ?? null,
                'assignee'    => $data['assignee']    ?? null,
                'notes'       => $data['notes']       ?? null,
                'id'          => $id,
            ]
        );

        // Registrar histórico dos campos alterados
        $track = ['status', 'priority', 'title', 'assignee', 'deadline'];
        foreach ($track as $field) {
            if (isset($data[$field]) && $current[$field] !== $data[$field]) {
                $this->history->record($id, $field, $current[$field], $data[$field], $userId);
            }
        }
        return true;
    }

    public function changeStatus(int $id, string $status, int $userId, string $comment = ''): bool
    {
        $current = $this->findById($id);
        if (!$current || $current['status'] === $status) return false;

        $completed = $status === 'concluida' ? ', completed_at = NOW()' : '';
        Database::query(
            "UPDATE demands SET status = ? {$completed} WHERE id = ?",
            [$status, $id]
        );
        $this->history->record($id, 'status', $current['status'], $status, $userId, $comment);
        return true;
    }

    public function softDelete(int $id, int $userId): bool
    {
        $current = $this->findById($id);
        if (!$current) return false;
        Database::query('UPDATE demands SET deleted_at = NOW() WHERE id = ?', [$id]);
        $this->history->record($id, 'exclusão', null, 'Demanda excluída', $userId);
        return true;
    }

    // ── Construtor de WHERE ───────────────────────────────────────────────────

    private function buildWhere(array $filters): array
    {
        $clauses = ['d.deleted_at IS NULL'];
        $params  = [];

        $allowed_status   = ['pendente','em_andamento','concluida','cancelada'];
        $allowed_priority = ['baixa','media','alta','critica'];

        if (!empty($filters['status']) && in_array($filters['status'], $allowed_status, true)) {
            $clauses[] = 'd.status = ?';
            $params[]  = $filters['status'];
        }
        if (!empty($filters['priority']) && in_array($filters['priority'], $allowed_priority, true)) {
            $clauses[] = 'd.priority = ?';
            $params[]  = $filters['priority'];
        }
        if (!empty($filters['category'])) {
            $clauses[] = 'd.category LIKE ?';
            $params[]  = '%' . $filters['category'] . '%';
        }
        if (!empty($filters['search'])) {
            $clauses[] = '(d.title LIKE ? OR d.description LIKE ?)';
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if (!empty($filters['date_from'])) {
            $clauses[] = 'DATE(d.created_at) >= ?';
            $params[]  = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $clauses[] = 'DATE(d.created_at) <= ?';
            $params[]  = $filters['date_to'];
        }

        $where = 'WHERE ' . implode(' AND ', $clauses);
        return [$where, $params];
    }
}
