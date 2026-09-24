<?php ob_start(); ?>
<section class="page-heading">
    <p class="eyebrow">Group · <?= (int) $group['member_count'] ?> <?= (int) $group['member_count'] === 1 ? 'member' : 'members' ?></p>
    <h1><?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?></h1>
    <p>Share this join code with people you trust: <strong><?= htmlspecialchars($group['join_code'], ENT_QUOTES, 'UTF-8') ?></strong></p>
    <div class="filters">
        <?php foreach (['planned' => 'Planned', 'all' => 'All', 'watching' => 'Watching', 'watched' => 'Watched'] as $value => $label): ?>
            <a class="<?= $filter === $value ? 'filter-active' : '' ?>" href="/groups/<?= (int) $group['id'] ?>?filter=<?= $value ?>"><?= $label ?></a>
        <?php endforeach; ?>
        <a href="/groups/<?= (int) $group['id'] ?>/watch-next">Watch next</a>
    </div>
</section>
<section class="group-meta">
    <div><span>Members</span><?php foreach ($members as $member): ?><p><?= htmlspecialchars($member['email'], ENT_QUOTES, 'UTF-8') ?><?= $member['role'] === 'owner' ? ' · owner' : '' ?></p><?php endforeach; ?></div>
    <div class="group-actions-list">
        <?php if ($group['role'] === 'member'): ?><form method="post" action="/groups/<?= (int) $group['id'] ?>/leave"><input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Infrastructure\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>"><button class="button button-quiet" type="submit">Leave group</button></form><?php endif; ?>
        <?php if ($group['role'] === 'owner'): ?><form method="post" action="/groups/<?= (int) $group['id'] ?>/delete" onsubmit="return confirm('Delete this group for all members?');"><input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Infrastructure\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>"><button class="button button-danger" type="submit">Delete group</button></form><?php endif; ?>
    </div>
</section>
<?php if ($items === []): ?>
<section class="empty-state"><div class="empty-mark">+</div><h2>No films in this view.</h2><p>When group members save films to their personal watchlists, the shared counts will appear here.</p></section>
<?php else: ?>
<section class="group-movie-grid" aria-label="Group movie aggregate">
    <?php foreach ($items as $item): ?>
        <article class="group-movie-card">
            <p class="movie-year"><?= htmlspecialchars(substr((string) $item['release_date'], 0, 4), ENT_QUOTES, 'UTF-8') ?></p>
            <h2><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= htmlspecialchars($item['overview'], ENT_QUOTES, 'UTF-8') ?></p>
            <div class="status-counts"><span>Planned <strong><?= (int) $item['planned_count'] ?></strong></span><span>Watching <strong><?= (int) $item['watching_count'] ?></strong></span><span>Watched <strong><?= (int) $item['watched_count'] ?></strong></span></div>
        </article>
    <?php endforeach; ?>
</section>
<?php endif; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
