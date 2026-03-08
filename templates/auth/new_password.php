<?php require __DIR__ . '/../layout.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <h2>Neues Passwort setzen</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="/new-password">
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
            <div class="mb-3">
                <label for="passwort">Neues Passwort</label>
                <input type="password" name="passwort" id="passwort" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="passwort2">Passwort wiederholen</label>
                <input type="password" name="passwort2" id="passwort2" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Passwort speichern</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout_end.php'; ?>