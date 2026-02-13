<?php $pageTitle = 'Tasks'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tasks</h2>
    <a href="<?= BASE_URL ?>/tasks/create" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Task
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="<?= BASE_URL ?>/tasks" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search tasks..." value="<?= sanitize($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <select name="project_id" class="form-select form-select-sm">
                    <option value="">All Projects</option>
                    <?php foreach ($projects as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($_GET['project_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                            <?= sanitize($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <?php foreach (['todo', 'in_progress', 'review', 'done'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>>
                            <?= ucfirst(str_replace('_', ' ', $s)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select form-select-sm">
                    <option value="">All Priority</option>
                    <?php foreach (['low', 'medium', 'high', 'critical'] as $p): ?>
                        <option value="<?= $p ?>" <?= ($_GET['priority'] ?? '') === $p ? 'selected' : '' ?>>
                            <?= ucfirst($p) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="<?= BASE_URL ?>/tasks" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($tasks)): ?>
            <p class="text-muted text-center py-4 mb-0">No tasks found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Project</th>
                            <th>Assignee</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Due</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><?= $task['id'] ?></td>
                                <td><?= sanitize($task['title']) ?></td>
                                <td><?= sanitize($task['project_name'] ?? 'N/A') ?></td>
                                <td><?= $task['assignee_name'] ? sanitize($task['assignee_name']) : '<span class="text-muted">Unassigned</span>' ?></td>
                                <td>
                                    <?php $sc = ['todo'=>'secondary','in_progress'=>'primary','review'=>'warning','done'=>'success']; ?>
                                    <span class="badge bg-<?= $sc[$task['status']] ?? 'secondary' ?>">
                                        <?= ucfirst(str_replace('_', ' ', sanitize($task['status']))) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php $pc = ['low'=>'success','medium'=>'info','high'=>'warning','critical'=>'danger']; ?>
                                    <span class="badge bg-<?= $pc[$task['priority']] ?? 'secondary' ?>">
                                        <?= ucfirst(sanitize($task['priority'])) ?>
                                    </span>
                                </td>
                                <td><?= $task['due_date'] ? sanitize($task['due_date']) : '-' ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/tasks/edit?id=<?= $task['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= BASE_URL ?>/tasks/delete" class="d-inline delete-form">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $task['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../components/pagination.php'; ?>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
