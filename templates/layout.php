<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SmartDoko</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/">SmartDoko</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/logout" class="btn btn-outline-light btn-sm">Logout</a>
        <?php endif; ?>
    </div>
</nav>
<main class="container mt-4">