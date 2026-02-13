<?php

class EmployeeController
{
    private Employee $model;
    private Validator $validator;

    public function __construct()
    {
        AuthMiddleware::requireAuth();
        $this->model = new Employee();
        $this->validator = new Validator();
    }

    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'search'     => $_GET['search'] ?? '',
            'department' => $_GET['department'] ?? '',
            'status'     => $_GET['status'] ?? '',
        ];

        $result     = $this->model->findAll($page, 10, $filters);
        $employees  = $result['data'];
        $pagination = new Pagination($result['total'], $page, 10);
        $departments = $this->model->getDepartments();

        require __DIR__ . '/../views/employees/index.php';
    }

    public function create(): void
    {
        AuthMiddleware::requireAdmin();
        require __DIR__ . '/../views/employees/create.php';
    }

    public function store(): void
    {
        AuthMiddleware::requireAdmin();
        CsrfMiddleware::check();

        $rules = [
            'first_name'  => 'required|max:50',
            'last_name'   => 'required|max:50',
            'email'       => 'required|email|max:100',
            'department'  => 'required|max:50',
            'designation' => 'required|max:50',
            'hire_date'   => 'required|date',
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            $_SESSION['errors'] = $this->validator->errors();
            $_SESSION['old'] = $_POST;
            redirect('/employees/create');
            return;
        }

        $data = [
            'first_name'  => trim($_POST['first_name']),
            'last_name'   => trim($_POST['last_name']),
            'email'       => trim($_POST['email']),
            'phone'       => trim($_POST['phone'] ?? ''),
            'department'  => trim($_POST['department']),
            'designation' => trim($_POST['designation']),
            'salary'      => !empty($_POST['salary']) ? (float)$_POST['salary'] : null,
            'hire_date'   => $_POST['hire_date'],
            'status'      => $_POST['status'] ?? 'active',
            'created_by'  => currentUserId(),
        ];

        $this->model->create($data);
        flash('success', 'Employee created successfully.');
        redirect('/employees');
    }

    public function edit(): void
    {
        AuthMiddleware::requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $employee = $this->model->findById($id);

        if (!$employee) {
            flash('error', 'Employee not found.');
            redirect('/employees');
            return;
        }

        require __DIR__ . '/../views/employees/edit.php';
    }

    public function update(): void
    {
        AuthMiddleware::requireAdmin();
        CsrfMiddleware::check();

        $id = (int)($_POST['id'] ?? 0);

        $rules = [
            'first_name'  => 'required|max:50',
            'last_name'   => 'required|max:50',
            'email'       => 'required|email|max:100',
            'department'  => 'required|max:50',
            'designation' => 'required|max:50',
            'hire_date'   => 'required|date',
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            $_SESSION['errors'] = $this->validator->errors();
            $_SESSION['old'] = $_POST;
            redirect("/employees/edit?id={$id}");
            return;
        }

        $data = [
            'first_name'  => trim($_POST['first_name']),
            'last_name'   => trim($_POST['last_name']),
            'email'       => trim($_POST['email']),
            'phone'       => trim($_POST['phone'] ?? ''),
            'department'  => trim($_POST['department']),
            'designation' => trim($_POST['designation']),
            'salary'      => !empty($_POST['salary']) ? (float)$_POST['salary'] : null,
            'hire_date'   => $_POST['hire_date'],
            'status'      => $_POST['status'] ?? 'active',
        ];

        $this->model->update($id, $data);
        flash('success', 'Employee updated successfully.');
        redirect('/employees');
    }

    public function destroy(): void
    {
        AuthMiddleware::requireAdmin();
        CsrfMiddleware::check();

        $id = (int)($_POST['id'] ?? 0);
        $this->model->delete($id);
        flash('success', 'Employee deleted.');
        redirect('/employees');
    }
}
