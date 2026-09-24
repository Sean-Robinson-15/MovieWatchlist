<?php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'MovieWatchlist', ENT_QUOTES, 'UTF-8') ?> · MovieWatchlist</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="/">Movie<span>Watchlist</span></a>
    <nav aria-label="Main navigation">
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="/search">Find films</a>
            <a href="/watchlist">My watchlist</a>
            <a href="/groups">Groups</a>
            <form method="post" action="/logout" class="inline-form"><input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Infrastructure\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>"><button type="submit" class="button button-quiet">Log out</button></form>
        <?php else: ?>
            <a href="/login">Log in</a>
            <a class="button button-small" href="/register">Create account</a>
        <?php endif; ?>
    </nav>
</header>
<main class="page-shell">
    <?php if ($error): ?>
        <div class="alert" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?= $content ?>
</main>
</body>
</html>
