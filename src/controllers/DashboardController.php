<?php

class DashboardController
{
    public function index(): void
    {
        AuthMiddleware::requireAuth();

        $db = Database::getConnection();

        // Stats for dashboard
        $stats = [
            'employees' => (int) $db->query('SELECT COUNT(*) FROM employees')->fetchColumn(),
            'projects'  => (int) $db->query('SELECT COUNT(*) FROM projects')->fetchColumn(),
            'tasks'     => (int) $db->query('SELECT COUNT(*) FROM tasks')->fetchColumn(),
            'tasks_done' => (int) $db->query("SELECT COUNT(*) FROM tasks WHERE status = 'done'")->fetchColumn(),
        ];

        // Recent tasks
        $recentTasks = $db->query(
            'SELECT t.*, p.name as project_name
             FROM tasks t
             LEFT JOIN projects p ON t.project_id = p.id
             ORDER BY t.created_at DESC LIMIT 5'
        )->fetchAll();

        require __DIR__ . '/../views/dashboard/index.php';
    }
}
