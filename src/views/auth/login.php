<?php $pageTitle = 'Login'; ob_start(); ?>

<div class="card shadow-sm">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold"><i class="bi bi-kanban"></i> <?= APP_NAME ?></h4>
            <p class="text-muted">Sign in to your account</p>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/login">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                       value="<?= old('username') ?>" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/guest.php'; ?>
