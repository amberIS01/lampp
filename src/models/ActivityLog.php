<?php

class ActivityLog
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function log(?int $userId, string $action, string $entityType, int $entityId, ?array $details = null): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO activity_log (user_id, action, entity_type, entity_id, details)
             VALUES (:user_id, :action, :entity_type, :entity_id, :details)'
        );
        $stmt->execute([
            'user_id'     => $userId,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'details'     => $details ? json_encode($details) : null,
        ]);
    }
}
