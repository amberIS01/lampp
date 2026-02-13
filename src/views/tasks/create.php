<?php $pageTitle = 'Add Task'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Add Task</h2>
    <a href="<?= BASE_URL ?>/tasks" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/tasks/store">
            <?= csrf_field() ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="project_id" class="form-label">Project *</label>
                    <select class="form-select" id="project_id" name="project_id" required>
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= old('project_id') == $p['id'] ? 'selected' : '' ?>>
                                <?= sanitize($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= errors('project_id') ?>
                </div>
                <div class="col-md-6">
                    <label for="assigned_to" class="form-label">Assign To</label>
                    <select class="form-select" id="assigned_to" name="assigned_to">
                        <option value="">Unassigned</option>
                        <?php foreach ($employees as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= old('assigned_to') == $e['id'] ? 'selected' : '' ?>>
                                <?= sanitize($e['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Title *</label>
                <input type="text" class="form-control" id="title" name="title"
                       value="<?= old('title') ?>" required>
                <?= errors('title') ?>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= old('description') ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-select" id="status" name="status" required>
                        <?php foreach (['todo', 'in_progress', 'review', 'done'] as $s): ?>
                            <option value="<?= $s ?>" <?= old('status', 'todo') === $s ? 'selected' : '' ?>>
                                <?= ucfirst(str_replace('_', ' ', $s)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= errors('status') ?>
                </div>
                <div class="col-md-4">
                    <label for="priority" class="form-label">Priority *</label>
                    <select class="form-select" id="priority" name="priority" required>
                        <?php foreach (['low', 'medium', 'high', 'critical'] as $p): ?>
                            <option value="<?= $p ?>" <?= old('priority', 'medium') === $p ? 'selected' : '' ?>>
                                <?= ucfirst($p) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= errors('priority') ?>
                </div>
                <div class="col-md-4">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="due_date" name="due_date"
                           value="<?= old('due_date') ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Task</button>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
