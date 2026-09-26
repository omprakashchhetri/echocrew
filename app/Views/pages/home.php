<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
  <?= $this->include('sections/hero') ?>
  <?= $this->include('sections/philosophy') ?>
  <?= $this->include('sections/systems') ?>
  <?= $this->include('sections/work') ?>
  <?= $this->include('sections/credibility') ?>
  <?= $this->include('sections/close') ?>
<?= $this->endSection() ?>
