<?= $this->extend('layouts/main') ?>

<?php
helper('blog');
$ts      = blog_post_date($post);
$url     = base_url('blog/view/' . $post['slug']);
$cover   = blog_cover_url($post);
$author  = $post['author_name'] ?: 'EchoCrew';
$shareTx = rawurlencode($post['title'] . ' | EchoCrew');
$ld      = [
    '@context'      => 'https://schema.org',
    '@type'         => 'BlogPosting',
    'headline'      => $post['title'],
    'description'   => $summary,
    'mainEntityOfPage' => $url,
    'datePublished' => $ts ? date('c', $ts) : null,
    'dateModified'  => ! empty($post['updated_at']) ? date('c', strtotime((string) $post['updated_at'])) : null,
    'author'        => ['@type' => 'Person', 'name' => $author],
    'publisher'     => ['@type' => 'Organization', 'name' => 'EchoCrew', 'logo' => ['@type' => 'ImageObject', 'url' => base_url('assets/img/ec/echocrew-logo.png')]],
    'image'         => $cover,
];
?>

<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/blog.css') ?>?v=1">
<script type="application/ld+json"><?= json_encode(array_filter($ld), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/blog.js') ?>?v=1" defer></script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="eb-progress" aria-hidden="true"></div>

<article data-eb-article>
  <header class="eb-post-head ec-shell">
    <nav class="ec-crumbs" aria-label="Breadcrumb">
      <a href="<?= base_url() ?>">Home</a><i aria-hidden="true">/</i>
      <a href="<?= base_url('blog') ?>">Blog</a><i aria-hidden="true">/</i>
      <span aria-current="page"><?= esc(mb_strimwidth($post['title'], 0, 40, '...')) ?></span>
    </nav>
    <?php if (! empty($post['category_name'])): ?>
      <a class="eb-pill" href="<?= base_url('blog/category/' . $post['category_slug']) ?>"><?= esc($post['category_name']) ?></a>
    <?php endif; ?>
    <h1><?= esc($post['title']) ?></h1>
    <?php if (! empty($post['excerpt'])): ?><p class="eb-lead"><?= esc($post['excerpt']) ?></p><?php endif; ?>

    <div class="eb-byline eb-meta">
      <span class="eb-author"><span class="eb-avatar" aria-hidden="true"><?= esc(blog_initials($author)) ?></span><?= esc($author) ?></span>
      <?php if ($ts): ?><span><time datetime="<?= date('Y-m-d', $ts) ?>"><?= date('j F Y', $ts) ?></time></span><?php endif; ?>
      <span><?= (int) $readTime ?> min read</span>
    </div>

    <?php if ($cover): ?>
      <figure class="eb-cover eb-hero-img"><?= blog_cover($post, true) ?></figure>
    <?php endif; ?>
  </header>

  <div class="ec-shell eb-layout">
    <aside class="eb-share" aria-label="Share this article">
      <span class="eb-share__label">Share</span>
      <a href="https://wa.me/?text=<?= $shareTx ?>%20<?= rawurlencode($url) ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on WhatsApp">WA</a>
      <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= rawurlencode($url) ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on LinkedIn">in</a>
      <a href="https://twitter.com/intent/tweet?text=<?= $shareTx ?>&url=<?= rawurlencode($url) ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on X">X</a>
      <button type="button" data-eb-copy="<?= esc($url, 'attr') ?>" aria-label="Copy link">Link</button>
    </aside>

    <div>
      <?php if (count($toc) > 1): ?>
        <details class="eb-toc-mobile">
          <summary>In this article</summary>
          <?php foreach ($toc as $t): ?><a href="#<?= esc($t['id'], 'attr') ?>"><?= esc($t['text']) ?></a><?php endforeach; ?>
        </details>
      <?php endif; ?>

      <!-- Post content is sanitised (App\Libraries\HtmlSanitizer) when saved in the admin area. -->
      <div class="ec-prose eb-prose"><?= $post['content'] ?></div>

      <?php if (! empty($tags)): ?>
        <div class="eb-tags">
          <?php foreach ($tags as $t): ?><a href="<?= base_url('blog/tag/' . $t['slug']) ?>">#<?= esc($t['name']) ?></a><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="eb-authorbox">
        <span class="eb-avatar" aria-hidden="true"><?= esc(blog_initials($author)) ?></span>
        <div><b>Written by <?= esc($author) ?></b><p>Part of the EchoCrew team in Siliguri, building custom software, CRM and automation for growing businesses.</p></div>
      </div>

      <div class="eb-postcta">
        <h2>Want this working in your business?</h2>
        <p>Tell us about your workflow and we will suggest a practical first step.</p>
        <a class="ec-btn ec-btn--primary" href="<?= base_url('#contact') ?>">Talk to us</a>
      </div>

      <section class="eb-comments" aria-labelledby="eb-comments-title">
        <h2 id="eb-comments-title">Comments<?= ! empty($comments) ? ' (' . count($comments) . ')' : '' ?></h2>

        <?php if (session()->getFlashdata('message')): ?><div class="ec-alert ec-alert--ok" role="status"><?= esc(session()->getFlashdata('message')) ?></div><?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?><div class="ec-alert ec-alert--bad" role="alert"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>

        <?php if (empty($comments)): ?>
          <p class="ec-muted">No comments yet. Start the conversation.</p>
        <?php else: ?>
          <?php foreach ($comments as $comment): ?>
            <div class="eb-comment">
              <span class="eb-avatar" aria-hidden="true"><?= esc(blog_initials($comment['username'])) ?></span>
              <div>
                <b><?= esc($comment['username']) ?></b>
                <?php if (! empty($comment['created_at'])): ?><time><?= esc(date('j M Y', strtotime((string) $comment['created_at']))) ?></time><?php endif; ?>
                <p><?= esc($comment['comment']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <?php if (auth()->loggedIn()): ?>
          <form class="ec-form" method="post" action="<?= site_url('blog/comment/' . $post['id']) ?>">
            <?= csrf_field() ?>
            <div class="ec-field">
              <label for="ec-comment">Add a comment</label>
              <textarea id="ec-comment" name="comment" required maxlength="2000"><?= esc(old('comment') ?? '') ?></textarea>
            </div>
            <?= (new \App\Libraries\Captcha())->render('comment') ?>
            <div><button class="ec-btn ec-btn--primary" type="submit">Post comment</button></div>
          </form>
        <?php else: ?>
          <p><a class="ec-link" href="<?= site_url('login') ?>">Log in to comment</a></p>
        <?php endif; ?>
      </section>
    </div>

    <?php if (count($toc) > 1): ?>
      <nav class="eb-toc" aria-label="Table of contents">
        <h2>In this article</h2>
        <ol><?php foreach ($toc as $t): ?><li><a href="#<?= esc($t['id'], 'attr') ?>"><?= esc($t['text']) ?></a></li><?php endforeach; ?></ol>
      </nav>
    <?php endif; ?>
  </div>
</article>

<?php if (! empty($related)): ?>
<section class="eb-related">
  <div class="ec-shell">
    <h2>Keep reading</h2>
    <div class="eb-grid">
      <?php foreach ($related as $r): ?>
        <a class="eb-card" href="<?= site_url('blog/view/' . $r['slug']) ?>">
          <div class="eb-cover"><?= blog_cover($r) ?></div>
          <div class="eb-card__body">
            <?php if (! empty($r['category_name'])): ?><span class="eb-card__cat"><?= esc($r['category_name']) ?></span><?php endif; ?>
            <h3><?= esc($r['title']) ?></h3>
            <div class="eb-meta"><span><?= blog_reading_time((string) $r['content']) ?> min read</span></div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
<?= $this->endSection() ?>
