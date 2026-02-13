<?php

class TaskController
{
    private Task $model;
    private Validator $validator;

    public function __construct()
    {
        AuthMiddleware::requireAuth();
        $this->model = new Task();
        $this->validator = new Validator();
    }

    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'search'      => $_GET['search'] ?? '',
            'status'      => $_GET['status'] ?? '',
            'priority'    => $_GET['priority'] ?? '',
            'project_id'  => $_GET['project_id'] ?? '',
            'assigned_to' => $_GET['assigned_to'] ?? '',
        ];

        $result     = $this->model->findAll($page, 10, $filters);
        $tasks      = $result['data'];
        $pagination = new Pagination($result['total'], $page, 10);

        // For filter dropdowns
        $projects  = (new Project())->findAllSimple();
        $employees = Database::getConnection()
            ->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM employees ORDER BY first_name")
            ->fetchAll();

        require __DIR__ . '/../views/tasks/index.php';
    }

    public function create(): void
    {
        $projects  = (new Project())->findAllSimple();
        $employees = Database::getConnection()
            ->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM employees WHERE status = 'active' ORDER BY first_name")
            ->fetchAll();

        require __DIR__ . '/../views/tasks/create.php';
    }

    public function store(): void
    {
        CsrfMiddleware::check();

        $rules = [
            'project_id' => 'required|numeric',
            'title'      => 'required|max:150',
            'status'     => 'required|in:todo,in_progress,review,done',
            'priority'   => 'required|in:low,medium,high,critical',
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            $_SESSION['errors'] = $this->validator->errors();
            $_SESSION['old'] = $_POST;
            redirect('/tasks/create');
            return;
        }

        $data = [
            'project_id'  => (int)$_POST['project_id'],
            'assigned_to' => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null,
            'title'       => trim($_POST['title']),
            'description' => trim($_POST['description'] ?? ''),
            'status'      => $_POST['status'],
            'priority'    => $_POST['priority'],
            'due_date'    => !empty($_POST['due_date']) ? $_POST['due_date'] : null,
            'created_by'  => currentUserId(),
        ];

        $this->model->create($data);
        flash('success', 'Task created successfully.');
        redirect('/tasks');
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $task = $this->model->findById($id);

        if (!$task) {
            flash('error', 'Task not found.');
            redirect('/tasks');
            return;
        }

        $projects  = (new Project())->findAllSimple();
        $employees = Database::getConnection()
            ->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM employees WHERE status = 'active' ORDER BY first_name")
            ->fetchAll();

        require __DIR__ . '/../views/tasks/edit.php';
    }

    public function update(): void
    {
        CsrfMiddleware::check();

        $id = (int)($_POST['id'] ?? 0);

        $rules = [
            'project_id' => 'required|numeric',
            'title'      => 'required|max:150',
            'status'     => 'required|in:todo,in_progress,review,done',
            'priority'   => 'required|in:low,medium,high,critical',
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            $_SESSION['errors'] = $this->validator->errors();
            $_SESSION['old'] = $_POST;
            redirect("/tasks/edit?id={$id}");
            return;
        }

        $data = [
            'project_id'  => (int)$_POST['project_id'],
            'assigned_to' => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null,
            'title'       => trim($_POST['title']),
            'description' => trim($_POST['description'] ?? ''),
            'status'      => $_POST['status'],
            'priority'    => $_POST['priority'],
            'due_date'    => !empty($_POST['due_date']) ? $_POST['due_date'] : null,
        ];

        $this->model->update($id, $data);
        flash('success', 'Task updated successfully.');
        redirect('/tasks');
    }

    public function destroy(): void
    {
        CsrfMiddleware::check();

        $id = (int)($_POST['id'] ?? 0);
        $this->model->delete($id);
        flash('success', 'Task deleted.');
        redirect('/tasks');
    }
}
