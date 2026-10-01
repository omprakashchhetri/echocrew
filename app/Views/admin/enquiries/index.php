<?= $this->extend('admin/layout') ?>
<?= $this->section('actions') ?>
  <a class="btn ghost sm" href="<?= site_url('admin/enquiries') ?>">All</a>
  <?php foreach (\App\Models\EnquiryModel::STATUSES as $s): ?><a class="btn ghost sm" href="<?= site_url('admin/enquiries?status=' . $s) ?>"><?= ucfirst($s) ?></a><?php endforeach; ?>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="card tbl"><table>
  <tr><th>From</th><th>Service</th><th>Status</th><th>Received</th></tr>
  <?php if (! $enquiries): ?><tr><td colspan="4" class="mute">No enquiries.</td></tr><?php endif; ?>
  <?php foreach ($enquiries as $e): ?>
  <tr>
    <td><a href="<?= site_url('admin/enquiries/' . $e['id']) ?>"><?= esc($e['name']) ?></a><br><span class="mute"><?= esc($e['company'] ?? '') ?> <?= esc($e['email']) ?></span></td>
    <td><?= esc($e['service'] ?? '') ?></td>
    <td><span class="pill <?= esc($e['status'], 'attr') ?>"><?= esc($e['status']) ?></span></td>
    <td class="mute"><?= esc(date('j M Y H:i', strtotime((string) $e['created_at']))) ?></td>
  </tr>
  <?php endforeach; ?>
</table><?= $pager->links() ?></div>
<?= $this->endSection() ?>
