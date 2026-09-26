<?php
$ecHome     = base_url();
$ecPath     = trim(uri_string(), '/');
$ecNavLinks = [
    'Services'     => base_url('services'),
    'Solutions'    => $ecHome . '#solutions',
    'Work'         => $ecHome . '#work',
    'Technologies' => $ecHome . '#technologies',
    'About'        => $ecHome . '#about',
    'Contact'      => $ecHome . '#contact',
];
$ecCurrent = static fn (string $label): bool => ($label === 'Services' && str_starts_with($ecPath, 'services'));
?>
<header class="ec-nav">
  <div class="ec-shell ec-nav__row">
    <a class="ec-nav__brand" href="<?= $ecHome ?>" aria-label="EchoCrew home">
      <img class="is-dark" src="<?= base_url('assets/img/ec/echocrew-logo.png') ?>" alt="EchoCrew" width="353" height="96">
      <img class="is-light" src="<?= base_url('assets/img/ec/echocrew-logo-light.png') ?>" alt="" width="353" height="96" aria-hidden="true">
    </a>

    <nav class="ec-nav__links" aria-label="Primary">
      <?php foreach ($ecNavLinks as $label => $href): ?>
        <a href="<?= $href ?>"<?= $ecCurrent($label) ? ' aria-current="page"' : '' ?>><?= $label ?></a>
      <?php endforeach; ?>
    </nav>

    <a class="ec-btn ec-btn--primary ec-btn--sm ec-nav__cta" href="<?= $ecHome ?>#contact">Start a project</a>

    <button class="ec-nav__burger" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="ec-mobilenav">
      <span></span><span></span>
    </button>
  </div>
</header>

<nav class="ec-mobilenav" id="ec-mobilenav" aria-label="Mobile">
  <?php foreach ($ecNavLinks as $label => $href): ?>
    <a href="<?= $href ?>"><?= $label ?></a>
  <?php endforeach; ?>
  <a href="<?= base_url('blog') ?>">Blog</a>
  <a class="ec-btn ec-btn--primary" href="<?= $ecHome ?>#contact">Start a project</a>
</nav>
