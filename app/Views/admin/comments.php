<?= $this->extend('admin/layout') ?>
<?= $this->section('actions') ?>
  <a class="btn ghost sm" href="<?= site_url('admin/comments') ?>">All</a>
  <a class="btn ghost sm" href="<?= site_url('admin/comments?status=approved') ?>">Visible</a>
  <a class="btn ghost sm" href="<?= site_url('admin/comments?status=hidden') ?>">Hidden</a>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="card tbl"><table>
  <tr><th>Comment</th><th>Post</th><th>Status</th><th></th></tr>
  <?php if (! $comments): ?><tr><td colspan="4" class="mute">No comments.</td></tr><?php endif; ?>
  <?php foreach ($comments as $c): ?>
  <tr>
    <td><b><?= esc($c['username'] ?? 'deleted user') ?></b> <span class="mute"><?= esc((string) $c['created_at']) ?></span><br><?= esc($c['comment']) ?></td>
    <td><?= esc($c['post_title'] ?? '(post removed)') ?></td>
    <td><span class="pill <?= esc($c['status'], 'attr') ?>"><?= esc($c['status']) ?></span></td>
    <td class="row">
      <form class="inline" method="post" action="<?= site_url('admin/comments/status/' . $c['id']) ?>"><?= csrf_field() ?>
        <input type="hidden" name="status" value="<?= $c['status'] === 'approved' ? 'hidden' : 'approved' ?>">
        <button class="btn ghost sm" type="submit"><?= $c['status'] === 'approved' ? 'Hide' : 'Show' ?></button></form>
      <form class="inline" method="post" action="<?= site_url('admin/comments/delete/' . $c['id']) ?>" onsubmit="return confirm('Delete this comment?')"><?= csrf_field() ?><button class="btn bad sm" type="submit">Delete</button></form>
    </td>
  </tr>
  <?php endforeach; ?>
</table><?= $pager->links() ?></div>
<?= $this->endSection() ?>
