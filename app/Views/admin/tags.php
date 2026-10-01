<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="two">
  <div class="card tbl"><table>
    <tr><th>Tag</th><th>Slug</th><th>Posts</th><th></th></tr>
    <?php if (! $tags): ?><tr><td colspan="4" class="mute">No tags yet.</td></tr><?php endif; ?>
    <?php foreach ($tags as $t): ?>
    <tr><td><?= esc($t['name']) ?></td><td class="mute"><?= esc($t['slug']) ?></td><td><?= (int) $t['post_count'] ?></td>
      <td><form class="inline" method="post" action="<?= site_url('admin/tags/delete/' . $t['id']) ?>" onsubmit="return confirm('Delete this tag?')"><?= csrf_field() ?><button class="btn bad sm" type="submit">Delete</button></form></td></tr>
    <?php endforeach; ?>
  </table></div>
  <form class="card" method="post" action="<?= site_url('admin/tags/store') ?>">
    <?= csrf_field() ?>
    <h2>Add tags</h2>
    <label for="name">Names (comma separated)</label>
    <input type="text" id="name" name="name" required maxlength="255">
    <p><button class="btn" type="submit">Add</button></p>
  </form>
</div>
<?= $this->endSection() ?>
