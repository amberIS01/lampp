<?php

class ProjectController
{
    private Project $model;
    private Validator $validator;

    public function __construct()
    {
        AuthMiddleware::requireAuth();
        $this->model = new Project();
        $this->validator = new Validator();
    }

    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'search'   => $_GET['search'] ?? '',
            'status'   => $_GET['status'] ?? '',
            'priority' => $_GET['priority'] ?? '',
        ];

        $result     = $this->model->findAll($page, 10, $filters);
        $projects   = $result['data'];
        $pagination = new Pagination($result['total'], $page, 10);

        require __DIR__ . '/../views/projects/index.php';
    }

    public function create(): void
    {
        AuthMiddleware::requireAdmin();
        require __DIR__ . '/../views/projects/create.php';
    }

    public function store(): void
    {
        AuthMiddleware::requireAdmin();
        CsrfMiddleware::check();

        $rules = [
            'name'   => 'required|max:100',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,critical',
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            $_SESSION['errors'] = $this->validator->errors();
            $_SESSION['old'] = $_POST;
            redirect('/projects/create');
            return;
        }

        $data = [
            'name'        => trim($_POST['name']),
            'description' => trim($_POST['description'] ?? ''),
            'status'      => $_POST['status'],
            'priority'    => $_POST['priority'],
            'start_date'  => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
            'end_date'    => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
            'created_by'  => currentUserId(),
        ];

        $this->model->create($data);
        flash('success', 'Project created successfully.');
        redirect('/projects');
    }

    public function edit(): void
    {
        AuthMiddleware::requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $project = $this->model->findById($id);

        if (!$project) {
            flash('error', 'Project not found.');
            redirect('/projects');
            return;
        }

        require __DIR__ . '/../views/projects/edit.php';
    }

    public function update(): void
    {
        AuthMiddleware::requireAdmin();
        CsrfMiddleware::check();

        $id = (int)($_POST['id'] ?? 0);

        $rules = [
            'name'   => 'required|max:100',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,critical',
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            $_SESSION['errors'] = $this->validator->errors();
            $_SESSION['old'] = $_POST;
            redirect("/projects/edit?id={$id}");
            return;
        }

        $data = [
            'name'        => trim($_POST['name']),
            'description' => trim($_POST['description'] ?? ''),
            'status'      => $_POST['status'],
            'priority'    => $_POST['priority'],
            'start_date'  => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
            'end_date'    => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
        ];

        $this->model->update($id, $data);
        flash('success', 'Project updated successfully.');
        redirect('/projects');
    }

    public function destroy(): void
    {
        AuthMiddleware::requireAdmin();
        CsrfMiddleware::check();

        $id = (int)($_POST['id'] ?? 0);
        $this->model->delete($id);
        flash('success', 'Project deleted.');
        redirect('/projects');
    }
}
