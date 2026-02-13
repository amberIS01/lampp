<?php $pageTitle = 'Projects'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Projects</h2>
    <?php if (isAdmin()): ?>
        <a href="<?= BASE_URL ?>/projects/create" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Project
        </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="<?= BASE_URL ?>/projects" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search projects..." value="<?= sanitize($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <?php foreach (['planning', 'active', 'on_hold', 'completed', 'cancelled'] as $s): ?>
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
                <a href="<?= BASE_URL ?>/projects" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($projects)): ?>
            <p class="text-muted text-center py-4 mb-0">No projects found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Start</th>
                            <th>End</th>
                            <?php if (isAdmin()): ?><th>Actions</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $proj): ?>
                            <tr>
                                <td><?= $proj['id'] ?></td>
                                <td><?= sanitize($proj['name']) ?></td>
                                <td>
                                    <?php
                                    $sc = ['planning'=>'info','active'=>'primary','on_hold'=>'warning','completed'=>'success','cancelled'=>'danger'];
                                    ?>
                                    <span class="badge bg-<?= $sc[$proj['status']] ?? 'secondary' ?>">
                                        <?= ucfirst(str_replace('_', ' ', sanitize($proj['status']))) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php $pc = ['low'=>'success','medium'=>'info','high'=>'warning','critical'=>'danger']; ?>
                                    <span class="badge bg-<?= $pc[$proj['priority']] ?? 'secondary' ?>">
                                        <?= ucfirst(sanitize($proj['priority'])) ?>
                                    </span>
                                </td>
                                <td><?= $proj['start_date'] ? sanitize($proj['start_date']) : '-' ?></td>
                                <td><?= $proj['end_date'] ? sanitize($proj['end_date']) : '-' ?></td>
                                <?php if (isAdmin()): ?>
                                <td>
                                    <a href="<?= BASE_URL ?>/projects/edit?id=<?= $proj['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= BASE_URL ?>/projects/delete" class="d-inline delete-form">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $proj['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                                <?php endif; ?>
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
