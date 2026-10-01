<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="two">
  <div class="card tbl"><table>
    <tr><th>Name</th><th>Slug</th><th>Posts</th><th></th></tr>
    <?php if (! $categories): ?><tr><td colspan="4" class="mute">No categories yet.</td></tr><?php endif; ?>
    <?php foreach ($categories as $c): ?>
    <tr><td><?= esc($c['name']) ?></td><td class="mute"><?= esc($c['slug']) ?></td><td><?= (int) $c['post_count'] ?></td>
      <td class="row"><a class="btn ghost sm" href="<?= site_url('admin/categories?edit=' . $c['id']) ?>">Rename</a>
        <form class="inline" method="post" action="<?= site_url('admin/categories/delete/' . $c['id']) ?>" onsubmit="return confirm('Delete this category?')"><?= csrf_field() ?><button class="btn bad sm" type="submit">Delete</button></form></td></tr>
    <?php endforeach; ?>
  </table></div>
  <form class="card" method="post" action="<?= site_url('admin/categories/save' . ($editing ? '/' . $editing['id'] : '')) ?>">
    <?= csrf_field() ?>
    <h2><?= $editing ? 'Rename category' : 'Add category' ?></h2>
    <label for="name">Name</label>
    <input type="text" id="name" name="name" required maxlength="100" value="<?= esc(old('name', $editing['name'] ?? ''), 'attr') ?>">
    <p><button class="btn" type="submit">Save</button> <?php if ($editing): ?><a class="btn ghost" href="<?= site_url('admin/categories') ?>">Cancel</a><?php endif; ?></p>
  </form>
</div>
<?= $this->endSection() ?>
