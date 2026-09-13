<?php ob_start(); ?>
<section class="hero">
    <p class="eyebrow">A calmer way to choose what is next</p>
    <h1>Keep the films you are <em>actually</em> going to watch.</h1>
    <p class="hero-copy">Search, save, and move through your personal watchlist without losing the thread between “one day” and tonight.</p>
    <div class="hero-actions">
        <?php if ($userEmail): ?>
            <a class="button" href="/watchlist">Open my watchlist</a>
        <?php else: ?>
            <a class="button" href="/register">Start a watchlist</a>
            <a class="text-link" href="/login">Already have an account? Log in</a>
        <?php endif; ?>
    </div>
</section>
<section class="feature-strip" aria-label="Application features">
    <article><span>01</span><h2>Find</h2><p>Bring movie metadata into one focused search.</p></article>
    <article><span>02</span><h2>Save</h2><p>Keep a personal list that belongs to you.</p></article>
    <article><span>03</span><h2>Finish</h2><p>Track what you watched and what is still waiting.</p></article>
</section>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
