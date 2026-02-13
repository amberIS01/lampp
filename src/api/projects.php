<?php

$model  = new Project();
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
                    echo json_encode(['error' => 'Project not found']);
                    break;
                }
                echo json_encode(['data' => $item]);
            } else {
                $page = max(1, (int)($_GET['page'] ?? 1));
                $filters = [
                    'search'   => $_GET['search'] ?? '',
                    'status'   => $_GET['status'] ?? '',
                    'priority' => $_GET['priority'] ?? '',
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
                'name'     => 'required|max:100',
                'status'   => 'required|in:planning,active,on_hold,completed,cancelled',
                'priority' => 'required|in:low,medium,high,critical',
            ];
            if (!$validator->validate($input, $rules)) {
                http_response_code(422);
                echo json_encode(['error' => 'Validation failed', 'errors' => $validator->errors()]);
                break;
            }
            $data = [
                'name'        => trim($input['name']),
                'description' => trim($input['description'] ?? ''),
                'status'      => $input['status'],
                'priority'    => $input['priority'],
                'start_date'  => !empty($input['start_date']) ? $input['start_date'] : null,
                'end_date'    => !empty($input['end_date']) ? $input['end_date'] : null,
                'created_by'  => currentUserId(),
            ];
            $newId = $model->create($data);
            http_response_code(201);
            echo json_encode(['message' => 'Project created', 'id' => $newId]);
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
                echo json_encode(['error' => 'Project not found']);
                break;
            }
            $data = [
                'name'        => trim($input['name'] ?? $existing['name']),
                'description' => trim($input['description'] ?? $existing['description'] ?? ''),
                'status'      => $input['status'] ?? $existing['status'],
                'priority'    => $input['priority'] ?? $existing['priority'],
                'start_date'  => $input['start_date'] ?? $existing['start_date'],
                'end_date'    => $input['end_date'] ?? $existing['end_date'],
            ];
            $model->update($id, $data);
            echo json_encode(['message' => 'Project updated']);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID required']);
                break;
            }
            $model->delete($id);
            echo json_encode(['message' => 'Project deleted']);
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
