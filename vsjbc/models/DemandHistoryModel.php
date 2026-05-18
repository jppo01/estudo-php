<?php
class DemandHistoryModel
{
    public function record(int $demandId, string $field, ?string $old, ?string $new, int $userId, string $comment = ''): void
    {
        Database::query(
            'INSERT INTO demand_history (demand_id, field_changed, old_value, new_value, comment, changed_by)
             VALUES (?, ?, ?, ?, ?, ?)',
            [$demandId, $field, $old, $new, $comment ?: null, $userId]
        );
    }

    public function getByDemand(int $demandId): array
    {
        return Database::fetchAll(
            'SELECT h.*, u.name AS user_name
             FROM demand_history h
             JOIN users u ON u.id = h.changed_by
             WHERE h.demand_id = ?
             ORDER BY h.changed_at DESC',
            [$demandId]
        );
    }
}
