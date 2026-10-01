<?= $this->extend('admin/layout') ?>
<?= $this->section('actions') ?><a class="btn" href="<?= site_url('admin/users/create') ?>">New user</a><?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="card tbl"><table>
  <tr><th>User</th><th>Groups</th><th>Status</th><th></th></tr>
  <?php foreach ($users as $u): $mine = (int) $u->id === (int) user_id(); ?>
  <tr>
    <td><b><?= esc($u->username) ?></b><br><span class="mute"><?= esc((string) $u->email) ?></span></td>
    <td><?= esc(implode(', ', $u->getGroups() ?? [])) ?></td>
    <td><?= $u->isBanned() ? '<span class="pill hidden">banned</span>' : '<span class="pill approved">active</span>' ?><?= $mine ? ' <span class="mute">(you)</span>' : '' ?></td>
    <td class="row">
      <?php if ($canManage($u)): ?>
        <a class="btn ghost sm" href="<?= site_url('admin/users/edit/' . $u->id) ?>">Edit</a>
        <?php if (! $mine): ?>
          <form class="inline" method="post" action="<?= site_url('admin/users/ban/' . $u->id) ?>"><?= csrf_field() ?><button class="btn ghost sm" type="submit"><?= $u->isBanned() ? 'Reinstate' : 'Ban' ?></button></form>
          <form class="inline" method="post" action="<?= site_url('admin/users/delete/' . $u->id) ?>" onsubmit="return confirm('Delete this user permanently?')"><?= csrf_field() ?><button class="btn bad sm" type="submit">Delete</button></form>
        <?php endif; ?>
      <?php else: ?><span class="mute">Super admin only</span><?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table></div>
<?= $this->endSection() ?>
