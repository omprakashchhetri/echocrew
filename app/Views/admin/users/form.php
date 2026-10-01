<?php $editing = $user !== null; ?>
<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<form class="card" style="max-width:520px" method="post" action="<?= site_url($editing ? 'admin/users/update/' . $user->id : 'admin/users/store') ?>">
  <?= csrf_field() ?>
  <?php if ($editing): ?>
    <p><b><?= esc($user->username) ?></b> <span class="mute"><?= esc((string) $user->email) ?></span></p>
  <?php else: ?>
    <label for="username">Username</label><input type="text" id="username" name="username" required value="<?= esc(old('username', ''), 'attr') ?>">
    <label for="email">Email</label><input type="email" id="email" name="email" required value="<?= esc(old('email', ''), 'attr') ?>">
  <?php endif; ?>
  <label for="password">Password <?= $editing ? '<span class="mute">(leave blank to keep)</span>' : '' ?></label>
  <input type="password" id="password" name="password" minlength="10" autocomplete="new-password" <?= $editing ? '' : 'required' ?>>
  <label for="group">Role</label>
  <?php $current = $editing ? ($user->getGroups()[0] ?? 'user') : 'user'; $self = $editing && (int) $user->id === (int) user_id(); ?>
  <select id="group" name="group" <?= $self ? 'disabled' : '' ?>>
    <?php foreach ($groups as $k => $title): ?><option value="<?= esc($k, 'attr') ?>" <?= old('group', $current) === $k ? 'selected' : '' ?>><?= esc($title) ?></option><?php endforeach; ?>
  </select>
  <?php if ($self): ?><p class="mute">You cannot change your own role.</p><input type="hidden" name="group" value="<?= esc($current, 'attr') ?>"><?php endif; ?>
  <p><button class="btn" type="submit">Save</button> <a class="btn ghost" href="<?= site_url('admin/users') ?>">Cancel</a></p>
</form>
<?= $this->endSection() ?>
