<h1>Blog</h1>

<?php foreach ($posts as $post): ?>
    <h2><a href="<?= site_url('blog/view/' . $post['slug']) ?>"><?= esc($post['title']) ?></a></h2>
    <p>Category: <?= esc($post['category_name']) ?> | Views: <?= esc($post['view_count']) ?> | Status: <?= esc($post['status']) ?></p>
    <hr>
<?php endforeach; ?>

<?= $pager->links() ?>
