<?php require __DIR__ . '/../layout.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <h2>Registrieren</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="/register">
            <div class="mb-3">
                <label for="vorname">Vorname</label>
                <input type="text" name="vorname" id="vorname" class="form-control"
                       value="<?= htmlspecialchars($_POST['vorname'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="nachname">Nachname</label>
                <input type="text" name="nachname" id="nachname" class="form-control"
                       value="<?= htmlspecialchars($_POST['nachname'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="email">E-Mail</label>
                <input type="email" name="email" id="email" class="form-control"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="passwort">Passwort</label>
                <input type="password" name="passwort" id="passwort" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="passwort2">Passwort wiederholen</label>
                <input type="password" name="passwort2" id="passwort2" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrieren</button>
            <a href="/login" class="btn btn-link">Bereits registriert?</a>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout_end.php'; ?>