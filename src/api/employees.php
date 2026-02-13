<?php

$model  = new Employee();
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$input  = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                $item = $model->findById($id);
                if (!$item) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Employee not found']);
                    break;
                }
                echo json_encode(['data' => $item]);
            } else {
                $page = max(1, (int)($_GET['page'] ?? 1));
                $filters = [
                    'search'     => $_GET['search'] ?? '',
                    'department' => $_GET['department'] ?? '',
                    'status'     => $_GET['status'] ?? '',
                ];
                $result = $model->findAll($page, 10, $filters);
                echo json_encode([
                    'data'    => $result['data'],
                    'total'   => $result['total'],
                    'page'    => $page,
                    'perPage' => 10,
                ]);
            }
            break;

        case 'POST':
            $validator = new Validator();
            $rules = [
                'first_name'  => 'required|max:50',
                'last_name'   => 'required|max:50',
                'email'       => 'required|email|max:100',
                'department'  => 'required|max:50',
                'designation' => 'required|max:50',
                'hire_date'   => 'required|date',
            ];
            if (!$validator->validate($input, $rules)) {
                http_response_code(422);
                echo json_encode(['error' => 'Validation failed', 'errors' => $validator->errors()]);
                break;
            }
            $data = [
                'first_name'  => trim($input['first_name']),
                'last_name'   => trim($input['last_name']),
                'email'       => trim($input['email']),
                'phone'       => trim($input['phone'] ?? ''),
                'department'  => trim($input['department']),
                'designation' => trim($input['designation']),
                'salary'      => isset($input['salary']) ? (float)$input['salary'] : null,
                'hire_date'   => $input['hire_date'],
                'status'      => $input['status'] ?? 'active',
                'created_by'  => currentUserId(),
            ];
            $newId = $model->create($data);
            http_response_code(201);
            echo json_encode(['message' => 'Employee created', 'id' => $newId]);
            break;

        case 'PUT':
        case 'PATCH':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID required']);
                break;
            }
            $existing = $model->findById($id);
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['error' => 'Employee not found']);
                break;
            }
            $data = [
                'first_name'  => trim($input['first_name'] ?? $existing['first_name']),
                'last_name'   => trim($input['last_name'] ?? $existing['last_name']),
                'email'       => trim($input['email'] ?? $existing['email']),
                'phone'       => trim($input['phone'] ?? $existing['phone'] ?? ''),
                'department'  => trim($input['department'] ?? $existing['department']),
                'designation' => trim($input['designation'] ?? $existing['designation']),
                'salary'      => isset($input['salary']) ? (float)$input['salary'] : $existing['salary'],
                'hire_date'   => $input['hire_date'] ?? $existing['hire_date'],
                'status'      => $input['status'] ?? $existing['status'],
            ];
            $model->update($id, $data);
            echo json_encode(['message' => 'Employee updated']);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID required']);
                break;
            }
            $model->delete($id);
            echo json_encode(['message' => 'Employee deleted']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    $msg = APP_ENV === 'development' ? $e->getMessage() : 'Database error';
    echo json_encode(['error' => $msg]);
}
