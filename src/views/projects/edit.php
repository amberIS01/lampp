<?php $pageTitle = 'Edit Project'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Project</h2>
    <a href="<?= BASE_URL ?>/projects" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/projects/update">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $project['id'] ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Project Name *</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?= old('name', $project['name']) ?>" required>
                <?= errors('name') ?>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $project['description'] ?? '') ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status" class="form-label">Status *</label>
                    <?php $cs = old('status', $project['status']); ?>
                    <select class="form-select" id="status" name="status" required>
                        <?php foreach (['planning', 'active', 'on_hold', 'completed', 'cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= $cs === $s ? 'selected' : '' ?>>
                                <?= ucfirst(str_replace('_', ' ', $s)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= errors('status') ?>
                </div>
                <div class="col-md-6">
                    <label for="priority" class="form-label">Priority *</label>
                    <?php $cp = old('priority', $project['priority']); ?>
                    <select class="form-select" id="priority" name="priority" required>
                        <?php foreach (['low', 'medium', 'high', 'critical'] as $p): ?>
                            <option value="<?= $p ?>" <?= $cp === $p ? 'selected' : '' ?>>
                                <?= ucfirst($p) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= errors('priority') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date"
                           value="<?= old('start_date', $project['start_date'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date"
                           value="<?= old('end_date', $project['end_date'] ?? '') ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Project</button>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
