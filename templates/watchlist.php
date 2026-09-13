<?php ob_start(); ?>
<section class="page-heading">
    <p class="eyebrow">Signed in as <?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?></p>
    <h1>My watchlist</h1>
    <p>Keep the films you want to see close, and give each one a place in the queue.</p>
    <div class="filters">
        <a href="/watchlist">All</a><a href="/watchlist?status=planned">Planned</a><a href="/watchlist?status=watching">Watching</a><a href="/watchlist?status=watched">Watched</a>
    </div>
</section>
<?php if ($items === []): ?>
<section class="empty-state">
    <div class="empty-mark">+</div>
    <h2>No films here yet.</h2>
    <p>Search for a film and save it here to begin.</p>
    <a class="button" href="/search">Find a film</a>
</section>
<?php else: ?>
<section class="watchlist-grid">
    <?php foreach ($items as $item): ?>
        <article class="watchlist-card">
            <p class="movie-year"><?= htmlspecialchars(substr((string) $item['release_date'], 0, 4), ENT_QUOTES, 'UTF-8') ?></p>
            <h2><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h2>
            <form method="post" action="/watchlist/update" class="form-stack compact-form">
                <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Infrastructure\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                <label>Status<select name="status"><option <?= $item['status'] === 'planned' ? 'selected' : '' ?>>planned</option><option <?= $item['status'] === 'watching' ? 'selected' : '' ?>>watching</option><option <?= $item['status'] === 'watched' ? 'selected' : '' ?>>watched</option></select></label>
                <label>Rating<select name="rating"><option value="">No rating</option><?php for ($rating = 1; $rating <= 5; $rating++): ?><option value="<?= $rating ?>" <?= (int) $item['rating'] === $rating ? 'selected' : '' ?>><?= $rating ?>/5</option><?php endfor; ?></select></label>
                <label>Notes<textarea name="notes" rows="3"><?= htmlspecialchars($item['notes'], ENT_QUOTES, 'UTF-8') ?></textarea></label>
                <button class="button button-small" type="submit">Save changes</button>
            </form>
            <form method="post" action="/watchlist/remove"><input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Infrastructure\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>"><button class="button button-quiet" type="submit">Remove</button></form>
        </article>
    <?php endforeach; ?>
</section>
<?php endif; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
