<?php
class DemandCommentModel
{
    public function add(int $demandId, int $userId, string $comment): void
    {
        Database::query(
            'INSERT INTO demand_comments (demand_id, user_id, comment) VALUES (?, ?, ?)',
            [$demandId, $userId, $comment]
        );
    }

    public function getByDemand(int $demandId): array
    {
        return Database::fetchAll(
            'SELECT c.*, u.name AS user_name
             FROM demand_comments c
             JOIN users u ON u.id = c.user_id
             WHERE c.demand_id = ?
             ORDER BY c.created_at ASC',
            [$demandId]
        );
    }
}
