<?= $this->extend('admin/layout') ?>
<?= $this->section('actions') ?><a class="btn ghost" href="<?= site_url('admin/enquiries') ?>">Back</a><?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="two">
  <div class="card">
    <table>
      <?php foreach (['name' => 'Name', 'company' => 'Company', 'email' => 'Email', 'phone' => 'Phone', 'service' => 'Service', 'current_system' => 'Current system', 'budget' => 'Budget', 'timeline' => 'Timeline', 'preferred_contact' => 'Preferred contact', 'created_at' => 'Received'] as $k => $l): ?>
        <?php if (($enquiry[$k] ?? '') !== ''): ?><tr><th><?= $l ?></th><td><?= $k === 'email' ? '<a href="mailto:' . esc($enquiry[$k], 'attr') . '">' . esc($enquiry[$k]) . '</a>' : esc((string) $enquiry[$k]) ?></td></tr><?php endif; ?>
      <?php endforeach; ?>
    </table>
    <h2 style="margin-top:1rem">Message</h2>
    <p style="white-space:pre-wrap"><?= esc($enquiry['message']) ?></p>
  </div>
  <div class="card">
    <h2>Status</h2>
    <p><span class="pill <?= esc($enquiry['status'], 'attr') ?>"><?= esc($enquiry['status']) ?></span></p>
    <form method="post" action="<?= site_url('admin/enquiries/status/' . $enquiry['id']) ?>" class="row"><?= csrf_field() ?>
      <select name="status" style="max-width:150px"><?php foreach (\App\Models\EnquiryModel::STATUSES as $s): ?><option value="<?= $s ?>" <?= $enquiry['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select>
      <button class="btn" type="submit">Update</button>
    </form>
    <form method="post" action="<?= site_url('admin/enquiries/delete/' . $enquiry['id']) ?>" onsubmit="return confirm('Delete this enquiry?')" style="margin-top:1rem"><?= csrf_field() ?><button class="btn bad sm" type="submit">Delete enquiry</button></form>
  </div>
</div>
<?= $this->endSection() ?>
