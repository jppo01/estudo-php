<?php
class GlpiTicketModel
{
    public function importFromCsv(array $rows, string $batch): array
    {
        $inserted = 0;
        $updated  = 0;
        $errors   = 0;

        foreach ($rows as $row) {
            if (empty($row['glpi_id'])) { $errors++; continue; }
            try {
                $exists = Database::fetchOne('SELECT id FROM glpi_tickets WHERE glpi_id = ?', [$row['glpi_id']]);
                if ($exists) {
                    Database::query(
                        'UPDATE glpi_tickets SET title=:title, description=:description, category=:category,
                         status=:status, priority=:priority, requester=:requester, assignee=:assignee,
                         glpi_created_at=:gca, glpi_updated_at=:gua, solution=:solution,
                         import_batch=:batch
                         WHERE glpi_id=:glpi_id',
                        $this->params($row, $batch)
                    );
                    $updated++;
                } else {
                    Database::query(
                        'INSERT INTO glpi_tickets
                         (glpi_id,title,description,category,status,priority,requester,assignee,
                          glpi_created_at,glpi_updated_at,solution,import_batch)
                         VALUES
                         (:glpi_id,:title,:description,:category,:status,:priority,:requester,:assignee,
                          :gca,:gua,:solution,:batch)',
                        $this->params($row, $batch)
                    );
                    $inserted++;
                }
            } catch (Exception) {
                $errors++;
            }
        }
        return compact('inserted', 'updated', 'errors');
    }

    private function params(array $r, string $batch): array
    {
        return [
            'glpi_id'     => $r['glpi_id'],
            'title'       => $r['title']       ?? '',
            'description' => $r['description'] ?? null,
            'category'    => $r['category']    ?? null,
            'status'      => $r['status']      ?? 'aberto',
            'priority'    => $r['priority']    ?? null,
            'requester'   => $r['requester']   ?? null,
            'assignee'    => $r['assignee']    ?? null,
            'gca'         => $r['glpi_created_at'] ?? null,
            'gua'         => $r['glpi_updated_at'] ?? null,
            'solution'    => $r['solution']    ?? null,
            'batch'       => $batch,
        ];
    }

    public function findAll(array $filters = []): array
    {
        $clauses = [];
        $params  = [];
        if (!empty($filters['search'])) {
            $clauses[] = '(title LIKE ? OR description LIKE ? OR requester LIKE ?)';
            $like = '%' . $filters['search'] . '%';
            $params = [$like, $like, $like];
        }
        if (!empty($filters['status'])) {
            $clauses[] = 'status = ?';
            $params[] = $filters['status'];
        }
        $where = $clauses ? 'WHERE ' . implode(' AND ', $clauses) : '';
        return Database::fetchAll("SELECT * FROM glpi_tickets {$where} ORDER BY imported_at DESC LIMIT 200", $params);
    }

    public function searchForContext(string $query, int $limit = 5): array
    {
        $like = '%' . $query . '%';
        return Database::fetchAll(
            'SELECT glpi_id, title, description, status, priority, requester, assignee, solution
             FROM glpi_tickets WHERE title LIKE ? OR description LIKE ?
             ORDER BY glpi_created_at DESC LIMIT ?',
            [$like, $like, $limit]
        );
    }

    public function getLastBatches(int $n = 5): array
    {
        return Database::fetchAll(
            'SELECT import_batch, COUNT(*) AS total, MAX(imported_at) AS imported_at
             FROM glpi_tickets GROUP BY import_batch ORDER BY imported_at DESC LIMIT ?',
            [$n]
        );
    }
}
