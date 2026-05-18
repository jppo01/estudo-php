<?php
class OracleKnowledgeModel
{
    public function findAll(): array
    {
        return Database::fetchAll(
            'SELECT k.*, u.name AS created_by_name FROM oracle_knowledge k
             JOIN users u ON u.id = k.created_by ORDER BY k.created_at DESC'
        );
    }

    public function findById(int $id): ?array
    {
        return Database::fetchOne('SELECT * FROM oracle_knowledge WHERE id = ?', [$id]);
    }

    public function searchRelevant(string $query, int $limit = 5): array
    {
        $like = '%' . $query . '%';
        return Database::fetchAll(
            'SELECT question, answer FROM oracle_knowledge
             WHERE active = 1 AND (question LIKE ? OR answer LIKE ? OR tags LIKE ?)
             LIMIT ?',
            [$like, $like, $like, $limit]
        );
    }

    public function create(array $data, int $userId): int
    {
        Database::query(
            'INSERT INTO oracle_knowledge (category, question, answer, tags, active, created_by)
             VALUES (:category, :question, :answer, :tags, :active, :created_by)',
            [
                'category'   => $data['category']   ?? null,
                'question'   => $data['question'],
                'answer'     => $data['answer'],
                'tags'       => $data['tags']        ?? null,
                'active'     => isset($data['active']) ? 1 : 0,
                'created_by' => $userId,
            ]
        );
        return (int)Database::lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        Database::query(
            'UPDATE oracle_knowledge SET category=:category, question=:question, answer=:answer,
             tags=:tags, active=:active, updated_at=NOW() WHERE id=:id',
            [
                'category' => $data['category'] ?? null,
                'question' => $data['question'],
                'answer'   => $data['answer'],
                'tags'     => $data['tags']     ?? null,
                'active'   => isset($data['active']) ? 1 : 0,
                'id'       => $id,
            ]
        );
        return true;
    }

    public function delete(int $id): void
    {
        Database::query('DELETE FROM oracle_knowledge WHERE id = ?', [$id]);
    }
}
