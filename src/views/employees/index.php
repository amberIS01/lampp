<?php $pageTitle = 'Employees'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Employees</h2>
    <?php if (isAdmin()): ?>
        <a href="<?= BASE_URL ?>/employees/create" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Employee
        </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="<?= BASE_URL ?>/employees" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search name or email..." value="<?= sanitize($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <select name="department" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= sanitize($dept) ?>" <?= ($_GET['department'] ?? '') === $dept ? 'selected' : '' ?>>
                            <?= sanitize($dept) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <?php foreach (['active', 'inactive', 'terminated'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>>
                            <?= ucfirst($s) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="<?= BASE_URL ?>/employees" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($employees)): ?>
            <p class="text-muted text-center py-4 mb-0">No employees found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Status</th>
                            <?php if (isAdmin()): ?><th>Actions</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $emp): ?>
                            <tr>
                                <td><?= $emp['id'] ?></td>
                                <td><?= sanitize($emp['first_name'] . ' ' . $emp['last_name']) ?></td>
                                <td><?= sanitize($emp['email']) ?></td>
                                <td><?= sanitize($emp['department']) ?></td>
                                <td><?= sanitize($emp['designation']) ?></td>
                                <td>
                                    <?php
                                    $colors = ['active' => 'success', 'inactive' => 'warning', 'terminated' => 'danger'];
                                    ?>
                                    <span class="badge bg-<?= $colors[$emp['status']] ?? 'secondary' ?>">
                                        <?= ucfirst(sanitize($emp['status'])) ?>
                                    </span>
                                </td>
                                <?php if (isAdmin()): ?>
                                <td>
                                    <a href="<?= BASE_URL ?>/employees/edit?id=<?= $emp['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= BASE_URL ?>/employees/delete" class="d-inline delete-form">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
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
