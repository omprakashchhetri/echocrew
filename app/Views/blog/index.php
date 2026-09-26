<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="ec-subhero">
  <div class="ec-shell">
    <nav class="ec-crumbs" aria-label="Breadcrumb"><a href="<?= base_url() ?>">Home</a><i aria-hidden="true">/</i><span aria-current="page">Blog</span></nav>
    <h1 data-split>Blog</h1>
    <p class="ec-lead" data-reveal>Writing from the EchoCrew team on software, automation and running digital systems.</p>
  </div>
</section>

<section class="ec-section" style="padding-top:0">
  <div class="ec-shell">
    <?php if (empty($posts)): ?>
      <div class="ec-empty">
        <h2>Nothing published yet</h2>
        <p>The first posts are being written. In the meantime, the services pages cover how we work.</p>
        <a class="ec-link" href="<?= base_url('services') ?>">Browse services</a>
      </div>
    <?php else: ?>
      <ul class="ec-posts">
        <?php foreach ($posts as $post): ?>
          <li data-reveal>
            <a href="<?= site_url('blog/view/' . $post['slug']) ?>">
              <?php if (! empty($post['created_at'])): ?>
                <time datetime="<?= esc(date('Y-m-d', strtotime((string) $post['created_at'])), 'attr') ?>"><?= esc(date('j M Y', strtotime((string) $post['created_at']))) ?></time>
              <?php else: ?>
                <span></span>
              <?php endif; ?>
              <h2><?= esc($post['title']) ?></h2>
              <span class="ec-posts__cat"><?= esc($post['category_name'] ?? '') ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
      <?= $pager->links() ?>
    <?php endif; ?>
  </div>
</section>

<?= $this->endSection() ?>
