<?php ob_start(); ?>
<section class="page-heading">
    <p class="eyebrow">Movie discovery</p>
    <h1>Find your next film.</h1>
    <form method="get" action="/search" class="search-form">
        <label class="sr-only" for="q">Search movies</label>
        <input id="q" name="q" value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search by title" autofocus>
        <button class="button" type="submit">Search</button>
    </form>
</section>
<?php if ($searchError): ?>
    <div class="alert" role="alert"><?= htmlspecialchars($searchError, ENT_QUOTES, 'UTF-8') ?></div>
<?php elseif ($query !== '' && $results === []): ?>
    <section class="empty-state"><h2>No results yet.</h2><p>Try another title, or check that your TMDB API key is configured.</p></section>
<?php elseif ($results): ?>
    <section class="movie-grid" aria-label="Search results">
        <?php foreach ($results as $movie): ?>
            <article class="movie-card">
                <div class="movie-poster" aria-hidden="true"></div>
                <div class="movie-card-body">
                    <p class="movie-year"><?= htmlspecialchars(substr((string) $movie['release_date'], 0, 4), ENT_QUOTES, 'UTF-8') ?></p>
                    <h2><?= htmlspecialchars($movie['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <p><?= htmlspecialchars($movie['overview'], ENT_QUOTES, 'UTF-8') ?></p>
                    <form method="post" action="/watchlist/add">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Infrastructure\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
                        <?php foreach (['tmdb_id', 'title', 'overview', 'poster_path', 'release_date'] as $field): ?>
                            <input type="hidden" name="<?= $field ?>" value="<?= htmlspecialchars((string) ($movie[$field] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php endforeach; ?>
                        <button class="button button-small" type="submit">Add to watchlist</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
