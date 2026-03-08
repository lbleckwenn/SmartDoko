<?php require __DIR__ . '/../layout.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <h2>Bestätigungslink erneut senden</h2>
        <?php if (isset($info)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($info) ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!isset($info)): ?>
            <form method="post" action="/resend-verify">
                <div class="mb-3">
                    <label for="email">E-Mail-Adresse</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Neuen Link senden</button>
                <a href="/login" class="btn btn-link">Zurück zum Login</a>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layout_end.php'; ?>