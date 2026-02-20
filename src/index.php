<?php

// =============================================
// Mini ERP - Front Controller
// =============================================

// Serve static files directly when using PHP built-in server
if (php_sapi_name() === 'cli-server') {
    $requestPath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($requestPath) && !str_ends_with($requestPath, '.php')) {
        return false; // Let the built-in server handle static files
    }
}

// Boot config
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/helpers/functions.php';

// Autoload classes from known directories
spl_autoload_register(function (string $class) {
    $dirs = ['models', 'controllers', 'middleware', 'helpers'];
    foreach ($dirs as $dir) {
        $file = __DIR__ . "/{$dir}/{$class}.php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Parse request
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Strip base path only when deployed in a subdirectory (e.g. Apache with /src prefix).
// Only strip if SCRIPT_NAME ends with index.php (avoids PHP built-in server
// misrouting when it resolves api/employees.php and sets SCRIPT_NAME to that).
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
if (str_ends_with($scriptName, '/index.php')) {
    $scriptDir = rtrim(dirname($scriptName), '/');
    if ($scriptDir && str_starts_with($uri, $scriptDir)) {
        $uri = substr($uri, strlen($scriptDir)) ?: '/';
    }
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// ---------- API Routes ----------
if (str_starts_with($uri, '/api/')) {
    header('Content-Type: application/json');

    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    match (true) {
        str_starts_with($uri, '/api/employees') => require __DIR__ . '/api/employees.php',
        str_starts_with($uri, '/api/projects')  => require __DIR__ . '/api/projects.php',
        str_starts_with($uri, '/api/tasks')     => require __DIR__ . '/api/tasks.php',
        default => (function () {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
        })(),
    };
    exit;
}

// ---------- Web Routes ----------
match ($uri) {
    '/'                  => redirect('/login'),
    '/login'             => (new AuthController())->login(),
    '/logout'            => (new AuthController())->logout(),
    '/dashboard'         => (new DashboardController())->index(),

    // Employees
    '/employees'         => (new EmployeeController())->index(),
    '/employees/create'  => (new EmployeeController())->create(),
    '/employees/store'   => (new EmployeeController())->store(),
    '/employees/edit'    => (new EmployeeController())->edit(),
    '/employees/update'  => (new EmployeeController())->update(),
    '/employees/delete'  => (new EmployeeController())->destroy(),

    // Projects
    '/projects'          => (new ProjectController())->index(),
    '/projects/create'   => (new ProjectController())->create(),
    '/projects/store'    => (new ProjectController())->store(),
    '/projects/edit'     => (new ProjectController())->edit(),
    '/projects/update'   => (new ProjectController())->update(),
    '/projects/delete'   => (new ProjectController())->destroy(),

    // Tasks
    '/tasks'             => (new TaskController())->index(),
    '/tasks/create'      => (new TaskController())->create(),
    '/tasks/store'       => (new TaskController())->store(),
    '/tasks/edit'        => (new TaskController())->edit(),
    '/tasks/update'      => (new TaskController())->update(),
    '/tasks/delete'      => (new TaskController())->destroy(),

    default              => (function () {
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
    })(),
};
