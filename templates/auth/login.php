<?php ob_start(); ?>
<section class="auth-panel">
    <p class="eyebrow">Your shelf is waiting</p>
    <h1>Welcome back.</h1>
    <form method="post" action="/login" class="form-stack">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Infrastructure\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
        <label>Email<input type="email" name="email" autocomplete="email" required></label>
        <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
        <button class="button" type="submit">Log in</button>
    </form>
    <p class="form-note">New here? <a href="/register">Create an account</a></p>
</section>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
