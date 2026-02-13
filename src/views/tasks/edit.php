<?php $pageTitle = 'Edit Task'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Task</h2>
    <a href="<?= BASE_URL ?>/tasks" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/tasks/update">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $task['id'] ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="project_id" class="form-label">Project *</label>
                    <?php $curProj = old('project_id', (string)$task['project_id']); ?>
                    <select class="form-select" id="project_id" name="project_id" required>
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $curProj == $p['id'] ? 'selected' : '' ?>>
                                <?= sanitize($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= errors('project_id') ?>
                </div>
                <div class="col-md-6">
                    <label for="assigned_to" class="form-label">Assign To</label>
                    <?php $curAssign = old('assigned_to', (string)($task['assigned_to'] ?? '')); ?>
                    <select class="form-select" id="assigned_to" name="assigned_to">
                        <option value="">Unassigned</option>
                        <?php foreach ($employees as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= $curAssign == $e['id'] ? 'selected' : '' ?>>
                                <?= sanitize($e['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Title *</label>
                <input type="text" class="form-control" id="title" name="title"
                       value="<?= old('title', $task['title']) ?>" required>
                <?= errors('title') ?>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $task['description'] ?? '') ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Status *</label>
                    <?php $curStatus = old('status', $task['status']); ?>
                    <select class="form-select" id="status" name="status" required>
                        <?php foreach (['todo', 'in_progress', 'review', 'done'] as $s): ?>
                            <option value="<?= $s ?>" <?= $curStatus === $s ? 'selected' : '' ?>>
                                <?= ucfirst(str_replace('_', ' ', $s)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= errors('status') ?>
                </div>
                <div class="col-md-4">
                    <label for="priority" class="form-label">Priority *</label>
                    <?php $curPriority = old('priority', $task['priority']); ?>
                    <select class="form-select" id="priority" name="priority" required>
                        <?php foreach (['low', 'medium', 'high', 'critical'] as $p): ?>
                            <option value="<?= $p ?>" <?= $curPriority === $p ? 'selected' : '' ?>>
                                <?= ucfirst($p) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= errors('priority') ?>
                </div>
                <div class="col-md-4">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="due_date" name="due_date"
                           value="<?= old('due_date', $task['due_date'] ?? '') ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Task</button>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
