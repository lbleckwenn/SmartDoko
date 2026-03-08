<?php require __DIR__ . '/../layout.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6 text-center">
        <h1 class="display-1 text-muted">404</h1>
        <h2>Seite nicht gefunden</h2>
        <p class="text-muted mt-3">
            Die aufgerufene Seite existiert nicht oder wurde verschoben.
        </p>
        <div class="mt-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/dashboard" class="btn btn-primary">Zur App</a>
            <?php else: ?>
                <a href="/" class="btn btn-primary">Zur Startseite</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout_end.php'; ?>