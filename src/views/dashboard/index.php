<?php $pageTitle = 'Dashboard'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Dashboard</h2>
    <span class="badge bg-<?= isAdmin() ? 'danger' : 'primary' ?>">
        <?= isAdmin() ? 'Admin' : 'User' ?>
    </span>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-people text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="mb-0"><?= $stats['employees'] ?></h3>
                        <small class="text-muted">Employees</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-folder text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="mb-0"><?= $stats['projects'] ?></h3>
                        <small class="text-muted">Projects</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-list-task text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="mb-0"><?= $stats['tasks'] ?></h3>
                        <small class="text-muted">Total Tasks</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-check-circle text-info fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="mb-0"><?= $stats['tasks_done'] ?></h3>
                        <small class="text-muted">Completed</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Tasks -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Recent Tasks</h5>
    </div>
    <div class="card-body">
        <?php if (empty($recentTasks)): ?>
            <p class="text-muted mb-0">No tasks yet. Create a project first, then add tasks.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentTasks as $task): ?>
                            <tr>
                                <td><?= sanitize($task['title']) ?></td>
                                <td><?= sanitize($task['project_name'] ?? 'N/A') ?></td>
                                <td>
                                    <?php
                                    $statusColors = ['todo' => 'secondary', 'in_progress' => 'primary', 'review' => 'warning', 'done' => 'success'];
                                    $color = $statusColors[$task['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $color ?>"><?= sanitize(str_replace('_', ' ', $task['status'])) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $priorityColors = ['low' => 'success', 'medium' => 'info', 'high' => 'warning', 'critical' => 'danger'];
                                    $pColor = $priorityColors[$task['priority']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $pColor ?>"><?= sanitize($task['priority']) ?></span>
                                </td>
                                <td><?= $task['due_date'] ? sanitize($task['due_date']) : '<span class="text-muted">-</span>' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
