<?php

class Employee
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
            $where[] = '(first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['department'])) {
            $where[] = 'department = :department';
            $params['department'] = $filters['department'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'status = :status';
            $params['status'] = $filters['status'];
        }

        $whereClause = implode(' AND ', $where);

        // Total count
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM employees WHERE {$whereClause}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Paginated results
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare(
            "SELECT * FROM employees WHERE {$whereClause} ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
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
        $stmt = $this->db->prepare('SELECT * FROM employees WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO employees (first_name, last_name, email, phone, department, designation, salary, hire_date, status, created_by)
             VALUES (:first_name, :last_name, :email, :phone, :department, :designation, :salary, :hire_date, :status, :created_by)'
        );
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare(
            'UPDATE employees SET first_name=:first_name, last_name=:last_name, email=:email,
             phone=:phone, department=:department, designation=:designation, salary=:salary,
             hire_date=:hire_date, status=:status WHERE id=:id'
        );
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM employees WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function getDepartments(): array
    {
        return $this->db->query('SELECT DISTINCT department FROM employees ORDER BY department')->fetchAll(PDO::FETCH_COLUMN);
    }

    public function count(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM employees')->fetchColumn();
    }
}
