<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="grid">
  <div class="stat"><b><?= $published ?></b><span>Published posts</span></div>
  <div class="stat"><b><?= $drafts ?></b><span>Drafts</span></div>
  <div class="stat"><b><?= $newEnquiries ?></b><span>New enquiries</span></div>
  <div class="stat"><b><?= $comments ?></b><span>Comments</span></div>
</div>
<div class="two">
  <div class="card tbl"><h2>Latest enquiries</h2>
    <?php if (! $recentEnquiries): ?><p class="mute">None yet.</p><?php else: ?>
    <table><?php foreach ($recentEnquiries as $e): ?>
      <tr><td><a href="<?= site_url('admin/enquiries/' . $e['id']) ?>"><?= esc($e['name']) ?></a><br><span class="mute"><?= esc($e['service'] ?? '') ?></span></td>
          <td><span class="pill <?= esc($e['status'], 'attr') ?>"><?= esc($e['status']) ?></span></td>
          <td class="mute"><?= esc(date('j M', strtotime((string) $e['created_at']))) ?></td></tr>
    <?php endforeach; ?></table><?php endif; ?>
  </div>
  <div class="card tbl"><h2>Top posts</h2>
    <?php if (! $topPosts): ?><p class="mute">No published posts.</p><?php else: ?>
    <table><?php foreach ($topPosts as $p): ?>
      <tr><td><a href="<?= site_url('admin/blog/edit/' . $p['id']) ?>"><?= esc($p['title']) ?></a></td><td><?= (int) $p['view_count'] ?> views</td></tr>
    <?php endforeach; ?></table><?php endif; ?>
  </div>
</div>
<div class="card tbl"><h2>Recently edited posts</h2>
  <table><?php foreach ($recentPosts as $p): ?>
    <tr><td><a href="<?= site_url('admin/blog/edit/' . $p['id']) ?>"><?= esc($p['title']) ?></a></td>
        <td><span class="pill <?= esc($p['status'], 'attr') ?>"><?= esc($p['status']) ?></span></td>
        <td class="mute"><?= esc($p['category_name'] ?? '') ?></td></tr>
  <?php endforeach; ?></table>
</div>
<?= $this->endSection() ?>
