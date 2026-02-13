<?php $pageTitle = 'Add Project'; ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Add Project</h2>
    <a href="<?= BASE_URL ?>/projects" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/projects/store">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="name" class="form-label">Project Name *</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?= old('name') ?>" required>
                <?= errors('name') ?>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= old('description') ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-select" id="status" name="status" required>
                        <?php foreach (['planning', 'active', 'on_hold', 'completed', 'cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= old('status', 'planning') === $s ? 'selected' : '' ?>>
                                <?= ucfirst(str_replace('_', ' ', $s)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= errors('status') ?>
                </div>
                <div class="col-md-6">
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
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date"
                           value="<?= old('start_date') ?>">
                </div>
                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date"
                           value="<?= old('end_date') ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Project</button>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
