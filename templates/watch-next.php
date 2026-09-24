<?php ob_start(); ?>
<section class="page-heading">
    <p class="eyebrow">Group · <?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?></p>
    <h1>Watch next.</h1>
    <p>Each member gets one vote. Choose a different film to move your vote, or choose your current pick again to remove it.</p>
    <div class="filters"><a href="/groups/<?= (int) $group['id'] ?>">Back to group</a></div>
</section>
<?php if ($items === []): ?>
<section class="empty-state"><div class="empty-mark">+</div><h2>Nothing to vote on yet.</h2><p>Films saved by group members will appear here.</p></section>
<?php else: ?>
<section class="group-movie-grid" aria-label="Watch next choices">
    <?php foreach ($items as $item): ?>
        <article class="group-movie-card <?= (int) $item['voted'] === 1 ? 'vote-selected' : '' ?>">
            <p class="movie-year"><?= (int) $item['vote_count'] ?> <?= (int) $item['vote_count'] === 1 ? 'vote' : 'votes' ?></p>
            <h2><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= htmlspecialchars($item['overview'], ENT_QUOTES, 'UTF-8') ?></p>
            <form method="post" action="/groups/<?= (int) $group['id'] ?>/vote">
                <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Infrastructure\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="movie_id" value="<?= (int) $item['id'] ?>">
                <button class="button button-small" type="submit"><?= (int) $item['voted'] === 1 ? 'Remove my vote' : 'Vote to watch next' ?></button>
            </form>
        </article>
    <?php endforeach; ?>
</section>
<?php endif; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
