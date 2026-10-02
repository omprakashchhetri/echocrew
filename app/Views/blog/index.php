<?= $this->extend('layouts/main') ?>

<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/blog.css') ?>?v=1">
<link rel="alternate" type="application/rss+xml" title="EchoCrew Blog" href="<?= base_url('blog/feed.xml') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
helper('blog');
$posts    = $posts ?? [];
$isFirst  = ($pager->getCurrentPage() ?? 1) === 1 && empty($q) && ! empty($posts);
$featured = $isFirst ? array_shift($posts) : null;
$card     = static function (array $post): string {
    ob_start(); ?>
    <a class="eb-card" href="<?= site_url('blog/view/' . $post['slug']) ?>">
      <div class="eb-cover"><?= blog_cover($post) ?></div>
      <div class="eb-card__body">
        <?php if (! empty($post['category_name'])): ?><span class="eb-card__cat"><?= esc($post['category_name']) ?></span><?php endif; ?>
        <h3><?= esc($post['title']) ?></h3>
        <p><?= esc($post['summary'] ?? '') ?></p>
        <div class="eb-meta">
          <?php if ($ts = blog_post_date($post)): ?><span><time datetime="<?= date('Y-m-d', $ts) ?>"><?= date('j M Y', $ts) ?></time></span><?php endif; ?>
          <span><?= blog_reading_time((string) $post['content']) ?> min read</span>
        </div>
      </div>
    </a>
    <?php return (string) ob_get_clean();
};
?>

<section class="eb-hero">
  <div class="ec-shell">
    <nav class="ec-crumbs" aria-label="Breadcrumb"><a href="<?= base_url() ?>">Home</a><i aria-hidden="true">/</i><span aria-current="page">Blog</span></nav>
    <h1><?= isset($heading) && $heading ? esc($heading) : 'Notes on <em>building</em> software that works' ?></h1>
    <p class="ec-lead">Practical writing from the EchoCrew team on custom software, CRM, automation and running digital systems for growing businesses.</p>

    <div class="eb-tools">
      <nav class="eb-chips" aria-label="Categories">
        <a href="<?= base_url('blog') ?>" <?= empty($activeCategory) && empty($heading) ? 'aria-current="true"' : '' ?>>All</a>
        <?php foreach ($categories ?? [] as $c): ?>
          <a href="<?= base_url('blog/category/' . $c['slug']) ?>" <?= ($activeCategory ?? null) === $c['slug'] ? 'aria-current="true"' : '' ?>><?= esc($c['name']) ?></a>
        <?php endforeach; ?>
      </nav>
      <form class="eb-search" method="get" action="<?= base_url('blog') ?>" role="search">
        <label class="ec-sr" for="eb-q" style="position:absolute;left:-9999px">Search articles</label>
        <input type="search" id="eb-q" name="q" value="<?= esc($q ?? '', 'attr') ?>" placeholder="Search articles" maxlength="80">
      </form>
    </div>
  </div>
</section>

<section class="ec-section" style="padding-top:clamp(1.5rem,3vw,2.5rem)">
  <div class="ec-shell">
    <?php if (empty($posts) && ! $featured): ?>
      <div class="eb-empty">
        <h2><?= ! empty($q) ? 'No articles match "' . esc($q) . '"' : 'Nothing published yet' ?></h2>
        <p class="ec-muted" style="margin-bottom:1.2rem"><?= ! empty($q) ? 'Try a different word, or browse all articles.' : 'The first posts are being written. In the meantime, the services pages cover how we work.' ?></p>
        <a class="ec-link" href="<?= base_url(! empty($q) ? 'blog' : 'services') ?>"><?= ! empty($q) ? 'All articles' : 'Browse services' ?></a>
      </div>
    <?php else: ?>
      <?php if ($featured): ?>
        <a class="eb-featured" href="<?= site_url('blog/view/' . $featured['slug']) ?>">
          <div class="eb-cover"><?= blog_cover($featured, true) ?></div>
          <div>
            <span class="eb-flag">Latest</span>
            <h2><?= esc($featured['title']) ?></h2>
            <p><?= esc($featured['summary'] ?? '') ?></p>
            <div class="eb-meta">
              <?php if (! empty($featured['category_name'])): ?><span><?= esc($featured['category_name']) ?></span><?php endif; ?>
              <?php if ($ts = blog_post_date($featured)): ?><span><time datetime="<?= date('Y-m-d', $ts) ?>"><?= date('j M Y', $ts) ?></time></span><?php endif; ?>
              <span><?= blog_reading_time((string) $featured['content']) ?> min read</span>
            </div>
          </div>
        </a>
      <?php endif; ?>

      <?php if ($posts): ?>
        <div class="eb-grid">
          <?php foreach ($posts as $post): ?><?= $card($post) ?><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?= $pager->links() ?>
    <?php endif; ?>

    <aside class="eb-cta">
      <div>
        <h2>Have a project in mind?</h2>
        <p>Tell us what is not working today. We will reply with a clear next step, not a sales deck.</p>
      </div>
      <a class="ec-btn ec-btn--primary" href="<?= base_url('#contact') ?>">Start a conversation</a>
    </aside>
  </div>
</section>
<?= $this->endSection() ?>
