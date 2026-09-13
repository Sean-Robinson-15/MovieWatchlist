<?php ob_start(); ?>
<section class="auth-panel">
    <p class="eyebrow">Start with one film</p>
    <h1>Create your account.</h1>
    <form method="post" action="/register" class="form-stack">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Infrastructure\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
        <label>Email<input type="email" name="email" autocomplete="email" required></label>
        <label>Password<input type="password" name="password" minlength="8" autocomplete="new-password" required><small>At least 8 characters.</small></label>
        <button class="button" type="submit">Create account</button>
    </form>
    <p class="form-note">Already registered? <a href="/login">Log in</a></p>
</section>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
