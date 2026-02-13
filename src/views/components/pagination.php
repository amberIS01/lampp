<?php if (isset($pagination) && $pagination->pages > 1): ?>
<nav>
    <ul class="pagination justify-content-center">
        <li class="page-item <?= !$pagination->hasPrev() ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $pagination->buildQuery($pagination->page - 1) ?>">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>

        <?php
        $start = max(1, $pagination->page - 2);
        $end   = min($pagination->pages, $pagination->page + 2);
        ?>

        <?php if ($start > 1): ?>
            <li class="page-item"><a class="page-link" href="<?= $pagination->buildQuery(1) ?>">1</a></li>
            <?php if ($start > 2): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i === $pagination->page ? 'active' : '' ?>">
                <a class="page-link" href="<?= $pagination->buildQuery($i) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($end < $pagination->pages): ?>
            <?php if ($end < $pagination->pages - 1): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
            <li class="page-item"><a class="page-link" href="<?= $pagination->buildQuery($pagination->pages) ?>"><?= $pagination->pages ?></a></li>
        <?php endif; ?>

        <li class="page-item <?= !$pagination->hasNext() ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $pagination->buildQuery($pagination->page + 1) ?>">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>
