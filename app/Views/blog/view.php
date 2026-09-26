<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<article>
  <header class="ec-subhero">
    <div class="ec-shell ec-article">
      <nav class="ec-crumbs" aria-label="Breadcrumb">
        <a href="<?= base_url() ?>">Home</a><i aria-hidden="true">/</i>
        <a href="<?= base_url('blog') ?>">Blog</a><i aria-hidden="true">/</i>
        <span aria-current="page"><?= esc($post['title']) ?></span>
      </nav>
      <h1 data-split style="font-size:clamp(2.2rem,4.6vw,3.8rem)"><?= esc($post['title']) ?></h1>
      <div class="ec-article__meta">
        <?php if (! empty($post['category_name'])): ?><span><?= esc($post['category_name']) ?></span><?php endif; ?>
        <?php if (! empty($post['created_at'])): ?>
          <time datetime="<?= esc(date('Y-m-d', strtotime((string) $post['created_at'])), 'attr') ?>"><?= esc(date('j F Y', strtotime((string) $post['created_at']))) ?></time>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <div class="ec-shell ec-article" style="padding-bottom:var(--sec)">
    <!-- Post content is authored in the admin area and rendered as HTML. -->
    <div class="ec-prose"><?= $post['content'] ?></div>

    <section class="ec-comments" aria-labelledby="ec-comments-title">
      <h2 id="ec-comments-title">Comments</h2>

      <?php if (session()->getFlashdata('message')): ?>
        <div class="ec-alert ec-alert--ok" role="status"><?= esc(session()->getFlashdata('message')) ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="ec-alert ec-alert--bad" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <?php if (empty($comments)): ?>
        <p class="ec-muted">No comments yet.</p>
      <?php else: ?>
        <?php foreach ($comments as $comment): ?>
          <div class="ec-comment">
            <b><?= esc($comment['username']) ?></b>
            <p><?= esc($comment['comment']) ?></p>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if (auth()->loggedIn()) : ?>
        <form class="ec-form" method="post" action="<?= site_url('blog/comment/' . $post['id']) ?>">
          <?= csrf_field() ?>
          <div class="ec-field">
            <label for="ec-comment">Add a comment</label>
            <textarea id="ec-comment" name="comment" required maxlength="2000"></textarea>
          </div>
          <div><button class="ec-btn ec-btn--primary" type="submit">Post comment</button></div>
        </form>
      <?php else: ?>
        <p><a class="ec-link" href="<?= site_url('login') ?>">Log in to comment</a></p>
      <?php endif; ?>
    </section>
  </div>
</article>

<?= $this->endSection() ?>
