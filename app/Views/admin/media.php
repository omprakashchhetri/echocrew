<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php $limit = \App\Libraries\ImageUploader::limitLabel(); ?>
<form class="card" method="post" action="<?= site_url('admin/media/store') ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <h2>Upload images</h2>
  <div class="row">
    <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple required style="max-width:420px">
    <button class="btn" type="submit">Upload</button>
  </div>
  <p class="mute" style="margin:.5rem 0 0">JPG, PNG, WebP or GIF, max <?= esc($limit) ?> each. Files are converted to WebP and resized to 1600 px wide.</p>
</form>

<p class="mute"><?= (int) $total ?> image<?= $total === 1 ? '' : 's' ?></p>

<?php if (! $items): ?>
  <div class="card"><p class="mute">No images yet. Upload some above, or add them from the post editor.</p></div>
<?php else: ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:1rem">
  <?php foreach ($items as $it): ?>
    <div class="card" style="padding:.7rem;margin:0;display:flex;flex-direction:column;gap:.5rem">
      <a href="<?= esc($it['url']) ?>" target="_blank" rel="noopener"><img src="<?= esc($it['url']) ?>" alt="" loading="lazy" style="width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:8px;background:#eee"></a>
      <div class="mute" style="font-size:.82rem"><?= (int) $it['w'] ?>&times;<?= (int) $it['h'] ?> &middot; <?= esc(max(1, (int) round($it['size'] / 1024))) ?> KB &middot; <?= esc(date('j M Y', $it['time'])) ?></div>
      <div style="font-size:.82rem">
        <?php if ($it['used']): ?>
          Used in: <?php foreach ($it['used'] as $i => $u): ?><?= $i ? ', ' : '' ?><a href="<?= site_url('admin/blog/edit/' . $u['id']) ?>"><?= esc(mb_strimwidth($u['title'], 0, 28, '...')) ?></a><?php endforeach; ?>
        <?php else: ?><span class="pill draft">unused</span><?php endif; ?>
      </div>
      <div class="row" style="margin-top:auto">
        <button class="btn ghost sm" type="button" data-copy="<?= esc($it['url']) ?>">Copy URL</button>
        <form class="inline" method="post" action="<?= site_url('admin/media/delete') ?>" onsubmit="return confirm('Delete this image?')">
          <?= csrf_field() ?><input type="hidden" name="path" value="<?= esc($it['path']) ?>">
          <button class="btn bad sm" type="submit" <?= $it['used'] ? 'disabled title="In use"' : '' ?>>Delete</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php if ($pages > 1): ?>
  <p class="row" style="margin-top:1rem">
    <?php if ($page > 1): ?><a class="btn ghost sm" href="<?= site_url('admin/media?page=' . ($page - 1)) ?>">&larr; Newer</a><?php endif; ?>
    <span class="mute">Page <?= $page ?> of <?= $pages ?></span>
    <?php if ($page < $pages): ?><a class="btn ghost sm" href="<?= site_url('admin/media?page=' . ($page + 1)) ?>">Older &rarr;</a><?php endif; ?>
  </p>
<?php endif; ?>
<?php endif; ?>

<script>
  document.querySelectorAll('[data-copy]').forEach(function (b) {
    b.addEventListener('click', function () {
      var url = b.getAttribute('data-copy'), done = function () { var t = b.textContent; b.textContent = 'Copied'; setTimeout(function () { b.textContent = t; }, 1500); };
      if (navigator.clipboard) { navigator.clipboard.writeText(url).then(done); } else { var i = document.createElement('input'); i.value = url; document.body.appendChild(i); i.select(); document.execCommand('copy'); i.remove(); done(); }
    });
  });
</script>
<?= $this->endSection() ?>
