<?= $this->extend('admin/layout') ?>
<?= $this->section('actions') ?><a class="btn" href="<?= site_url('admin/blog/create') ?>">New post</a><?= $this->endSection() ?>
<?= $this->section('content') ?>
<form class="card row" method="get">
  <input type="search" name="q" placeholder="Search title" value="<?= esc($filters['q'], 'attr') ?>" style="max-width:240px">
  <select name="status" style="max-width:150px">
    <option value="">All statuses</option>
    <?php foreach (['draft', 'published', 'archived'] as $s): ?><option value="<?= $s ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
  </select>
  <select name="category" style="max-width:190px">
    <option value="0">All categories</option>
    <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= $filters['category'] === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option><?php endforeach; ?>
  </select>
  <button class="btn ghost" type="submit">Filter</button>
</form>
<div class="card tbl">
<table>
  <tr><th>Title</th><th>Category</th><th>Status</th><th>Views</th><th>Updated</th><th></th></tr>
  <?php if (! $posts): ?><tr><td colspan="6" class="mute">No posts found.</td></tr><?php endif; ?>
  <?php foreach ($posts as $post): ?>
  <tr>
    <td><a href="<?= site_url('admin/blog/edit/' . $post['id']) ?>"><?= esc($post['title']) ?></a><br><span class="mute">/<?= esc($post['slug']) ?></span></td>
    <td><?= esc($post['category_name'] ?? '') ?></td>
    <td><span class="pill <?= esc($post['status'], 'attr') ?>"><?= esc($post['status']) ?></span></td>
    <td><?= (int) $post['view_count'] ?></td>
    <td class="mute"><?= esc(date('j M Y', strtotime((string) $post['updated_at']))) ?></td>
    <td class="row">
      <?php if ($post['status'] === 'published'): ?>
        <a class="btn ghost sm" target="_blank" rel="noopener" href="<?= site_url('blog/view/' . $post['slug']) ?>">View</a>
      <?php endif; ?>
      <form class="inline" method="post" action="<?= site_url('admin/blog/status/' . $post['id']) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="status" value="<?= $post['status'] === 'published' ? 'draft' : 'published' ?>">
        <button class="btn ghost sm" type="submit"><?= $post['status'] === 'published' ? 'Unpublish' : 'Publish' ?></button>
      </form>
      <form class="inline" method="post" action="<?= site_url('admin/blog/delete/' . $post['id']) ?>" onsubmit="return confirm('Delete this post and its comments?')">
        <?= csrf_field() ?><button class="btn bad sm" type="submit">Delete</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?= $pager->links() ?>
</div>
<?= $this->endSection() ?>
