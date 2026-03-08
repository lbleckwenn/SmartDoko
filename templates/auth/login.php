<?php require __DIR__ . '/../layout.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h2>SmartDoko Login</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="/login">
                <div class="mb-3">
                    <label for="email">E-Mail</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="passwort">Passwort</label>
                    <input type="password" name="passwort" id="passwort" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Einloggen</button>
            </form>
        </div>
    </div>
</div>