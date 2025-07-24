<h1><?= esc($post['title']) ?></h1>
<p><em><?= esc($post['category_name']) ?> | Views: <?= esc($post['view_count']) ?></em></p>

<div><?= $post['content'] ?></div>

<h3>Comments</h3>
<?php foreach ($comments as $comment): ?>
    <p><strong><?= esc($comment['username']) ?>:</strong> <?= esc($comment['comment']) ?></p>
<?php endforeach; ?>

<?php if (auth()->loggedIn()) : ?>

    <form method="post" action="<?= site_url('blog/comment/' . $post['id']) ?>">
        <?= csrf_field() ?>
        <textarea name="comment" required></textarea><br>
        <button type="submit">Post Comment</button>
    </form>
<?php else: ?>
    <p><a href="<?= site_url('login') ?>">Login</a> to comment</p>
<?php endif; ?>
