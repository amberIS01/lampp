<?php

$model  = new Task();
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
                    echo json_encode(['error' => 'Task not found']);
                    break;
                }
                echo json_encode(['data' => $item]);
            } else {
                $page = max(1, (int)($_GET['page'] ?? 1));
                $filters = [
                    'search'      => $_GET['search'] ?? '',
                    'status'      => $_GET['status'] ?? '',
                    'priority'    => $_GET['priority'] ?? '',
                    'project_id'  => $_GET['project_id'] ?? '',
                    'assigned_to' => $_GET['assigned_to'] ?? '',
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
                'project_id' => 'required|numeric',
                'title'      => 'required|max:150',
                'status'     => 'required|in:todo,in_progress,review,done',
                'priority'   => 'required|in:low,medium,high,critical',
            ];
            if (!$validator->validate($input, $rules)) {
                http_response_code(422);
                echo json_encode(['error' => 'Validation failed', 'errors' => $validator->errors()]);
                break;
            }
            $data = [
                'project_id'  => (int)$input['project_id'],
                'assigned_to' => !empty($input['assigned_to']) ? (int)$input['assigned_to'] : null,
                'title'       => trim($input['title']),
                'description' => trim($input['description'] ?? ''),
                'status'      => $input['status'],
                'priority'    => $input['priority'],
                'due_date'    => !empty($input['due_date']) ? $input['due_date'] : null,
                'created_by'  => currentUserId(),
            ];
            $newId = $model->create($data);
            http_response_code(201);
            echo json_encode(['message' => 'Task created', 'id' => $newId]);
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
                echo json_encode(['error' => 'Task not found']);
                break;
            }
            $data = [
                'project_id'  => (int)($input['project_id'] ?? $existing['project_id']),
                'assigned_to' => isset($input['assigned_to']) ? ((int)$input['assigned_to'] ?: null) : $existing['assigned_to'],
                'title'       => trim($input['title'] ?? $existing['title']),
                'description' => trim($input['description'] ?? $existing['description'] ?? ''),
                'status'      => $input['status'] ?? $existing['status'],
                'priority'    => $input['priority'] ?? $existing['priority'],
                'due_date'    => array_key_exists('due_date', $input) ? ($input['due_date'] ?: null) : $existing['due_date'],
            ];
            $model->update($id, $data);
            echo json_encode(['message' => 'Task updated']);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID required']);
                break;
            }
            $model->delete($id);
            echo json_encode(['message' => 'Task deleted']);
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
