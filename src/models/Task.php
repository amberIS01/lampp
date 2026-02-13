<?php

class Task
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(int $page = 1, int $perPage = 10, array $filters = []): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(t.title LIKE :search OR t.description LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status'])) {
            $where[] = 't.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $where[] = 't.priority = :priority';
            $params['priority'] = $filters['priority'];
        }
        if (!empty($filters['project_id'])) {
            $where[] = 't.project_id = :project_id';
            $params['project_id'] = (int)$filters['project_id'];
        }
        if (!empty($filters['assigned_to'])) {
            $where[] = 't.assigned_to = :assigned_to';
            $params['assigned_to'] = (int)$filters['assigned_to'];
        }

        $whereClause = implode(' AND ', $where);

        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM tasks t WHERE {$whereClause}"
        );
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT t.*, p.name as project_name,
                    CONCAT(e.first_name, ' ', e.last_name) as assignee_name
             FROM tasks t
             LEFT JOIN projects p ON t.project_id = p.id
             LEFT JOIN employees e ON t.assigned_to = e.id
             WHERE {$whereClause}
             ORDER BY t.created_at DESC
             LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'  => $stmt->fetchAll(),
            'total' => $total,
        ];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, p.name as project_name,
                    CONCAT(e.first_name, \' \', e.last_name) as assignee_name
             FROM tasks t
             LEFT JOIN projects p ON t.project_id = p.id
             LEFT JOIN employees e ON t.assigned_to = e.id
             WHERE t.id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO tasks (project_id, assigned_to, title, description, status, priority, due_date, created_by)
             VALUES (:project_id, :assigned_to, :title, :description, :status, :priority, :due_date, :created_by)'
        );
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare(
            'UPDATE tasks SET project_id=:project_id, assigned_to=:assigned_to, title=:title,
             description=:description, status=:status, priority=:priority, due_date=:due_date WHERE id=:id'
        );
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM tasks WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM tasks')->fetchColumn();
    }
}
