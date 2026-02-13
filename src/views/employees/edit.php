<?php $pageTitle = 'Edit Employee'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Employee</h2>
    <a href="<?= BASE_URL ?>/employees" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/employees/update">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $employee['id'] ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First Name *</label>
                    <input type="text" class="form-control" id="first_name" name="first_name"
                           value="<?= old('first_name', $employee['first_name']) ?>" required>
                    <?= errors('first_name') ?>
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last Name *</label>
                    <input type="text" class="form-control" id="last_name" name="last_name"
                           value="<?= old('last_name', $employee['last_name']) ?>" required>
                    <?= errors('last_name') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?= old('email', $employee['email']) ?>" required>
                    <?= errors('email') ?>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                           value="<?= old('phone', $employee['phone'] ?? '') ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="department" class="form-label">Department *</label>
                    <input type="text" class="form-control" id="department" name="department"
                           value="<?= old('department', $employee['department']) ?>" required>
                    <?= errors('department') ?>
                </div>
                <div class="col-md-6">
                    <label for="designation" class="form-label">Designation *</label>
                    <input type="text" class="form-control" id="designation" name="designation"
                           value="<?= old('designation', $employee['designation']) ?>" required>
                    <?= errors('designation') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="salary" class="form-label">Salary</label>
                    <input type="number" step="0.01" class="form-control" id="salary" name="salary"
                           value="<?= old('salary', $employee['salary'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="hire_date" class="form-label">Hire Date *</label>
                    <input type="date" class="form-control" id="hire_date" name="hire_date"
                           value="<?= old('hire_date', $employee['hire_date']) ?>" required>
                    <?= errors('hire_date') ?>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <?php $currentStatus = old('status', $employee['status']); ?>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $currentStatus === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="terminated" <?= $currentStatus === 'terminated' ? 'selected' : '' ?>>Terminated</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Update Employee
            </button>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
