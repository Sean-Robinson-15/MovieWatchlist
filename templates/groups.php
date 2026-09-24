<?php ob_start(); ?>
<section class="page-heading">
    <p class="eyebrow">Your shared shelves</p>
    <h1>My groups</h1>
    <p>Compare what your people are planning, watching, and finishing together.</p>
</section>
<section class="group-actions">
    <form method="post" action="/groups/create" class="inline-panel">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Infrastructure\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
        <label>Create a group<input name="name" maxlength="80" placeholder="Friday film club" required></label>
        <button class="button button-small" type="submit">Create group</button>
    </form>
    <form method="post" action="/groups/join" class="inline-panel">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Infrastructure\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
        <label>Join with a code<input name="join_code" maxlength="6" placeholder="ABC123" required></label>
        <button class="button button-small" type="submit">Join group</button>
    </form>
</section>
<?php if ($groups === []): ?>
<section class="empty-state"><div class="empty-mark">+</div><h2>No groups yet.</h2><p>Create one for your next film night, or join a group with its code.</p></section>
<?php else: ?>
<section class="group-grid" aria-label="Groups you belong to">
    <?php foreach ($groups as $group): ?>
        <a class="group-card" href="/groups/<?= (int) $group['id'] ?>">
            <p class="movie-year"><?= (int) $group['member_count'] ?> <?= (int) $group['member_count'] === 1 ? 'member' : 'members' ?></p>
            <h2><?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?></h2>
            <span><?= $group['role'] === 'owner' ? 'Owner' : 'Member' ?> · Open group</span>
        </a>
    <?php endforeach; ?>
</section>
<?php endif; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
