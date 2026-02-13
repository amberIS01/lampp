<nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= BASE_URL ?>/dashboard">
            <i class="bi bi-kanban"></i> <?= APP_NAME ?>
        </a>

        <div class="d-flex align-items-center">
            <span class="text-light me-3">
                <i class="bi bi-person-circle"></i>
                <?= sanitize($_SESSION['username'] ?? '') ?>
                <span class="badge bg-<?= isAdmin() ? 'danger' : 'secondary' ?> ms-1"><?= $_SESSION['user_role'] ?? '' ?></span>
            </span>
            <a href="<?= BASE_URL ?>/logout" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>
