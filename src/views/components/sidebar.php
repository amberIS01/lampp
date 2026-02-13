<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($scriptDir && str_starts_with($currentPath, $scriptDir)) {
    $currentPath = substr($currentPath, strlen($scriptDir)) ?: '/';
}

function navActive(string $path, string $current): string {
    return str_starts_with($current, $path) ? 'active' : '';
}
?>

<nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" style="padding-top: 56px; min-height: 100vh;">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= navActive('/dashboard', $currentPath) ?>" href="<?= BASE_URL ?>/dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= navActive('/employees', $currentPath) ?>" href="<?= BASE_URL ?>/employees">
                    <i class="bi bi-people"></i> Employees
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= navActive('/projects', $currentPath) ?>" href="<?= BASE_URL ?>/projects">
                    <i class="bi bi-folder"></i> Projects
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= navActive('/tasks', $currentPath) ?>" href="<?= BASE_URL ?>/tasks">
                    <i class="bi bi-list-task"></i> Tasks
                </a>
            </li>
        </ul>
    </div>
</nav>
