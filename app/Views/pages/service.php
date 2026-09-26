<?php
// Rotate the supporting photo by service so neighbouring pages don't look identical.
$ecPhotos = [
    ['team-office.webp', 'A product team working together at their desks', 1600, 1068],
    ['pair-review.webp', 'Two people reviewing work on a laptop', 950, 960],
    ['team-discussion.webp', 'Colleagues talking through a problem around a laptop', 674, 450],
    ['at-the-desk.webp', 'A developer smiling at her desk', 670, 877],
    ['planning-wall.webp', 'A team planning work with sticky notes on a board', 510, 562],
];
$ecPhoto = $ecPhotos[crc32($slug) % count($ecPhotos)];
// Sentence-case a service name without flattening acronyms (CRM, API, AI, SEO).
$ecLower = static fn (string $s): string => preg_replace_callback('/\p{L}+/u', static fn ($m) => (strlen($m[0]) > 1 && strtoupper($m[0]) === $m[0]) ? $m[0] : strtolower($m[0]), $s);
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="ec-subhero" data-scope>
  <div class="ec-shell ec-subhero__grid">
    <div>
      <nav class="ec-crumbs" aria-label="Breadcrumb">
        <?php $last = array_key_last($breadcrumbs); foreach ($breadcrumbs as $label => $url): ?>
          <?php if ($label === $last): ?>
            <span aria-current="page"><?= esc($label) ?></span>
          <?php else: ?>
            <a href="<?= esc($url, 'url') ?>"><?= esc($label) ?></a><i aria-hidden="true">/</i>
          <?php endif; ?>
        <?php endforeach; ?>
      </nav>

      <h1 data-split><?= esc($service['h1']) ?></h1>
      <p class="ec-lead" data-reveal><?= esc($service['lead']) ?></p>
      <div class="ec-subhero__actions" data-reveal>
        <a class="ec-btn ec-btn--primary" href="<?= base_url() ?>#contact">Start a project</a>
        <a class="ec-link" href="<?= base_url() ?>#work">See our work</a>
      </div>
    </div>

    <figure class="ec-media ec-subhero__photo" data-unveil data-parallax="-24">
      <img src="<?= base_url('assets/img/ec/' . $ecPhoto[0]) ?>" alt="<?= esc($ecPhoto[1], 'attr') ?>" width="<?= $ecPhoto[2] ?>" height="<?= $ecPhoto[3] ?>" fetchpriority="high">
    </figure>
  </div>
</section>

<section class="ec-section">
  <div class="ec-shell ec-split">
    <h2 data-split>Inside <?= stripos('aeiou', $service['name'][0]) !== false ? 'an' : 'a' ?> <?= esc($ecLower($service['name'])) ?> project</h2>
    <ol class="ec-points">
      <?php foreach ($service['points'] as $point): ?><li data-reveal><?= esc($point) ?></li><?php endforeach; ?>
    </ol>
  </div>
</section>

<?php if (! empty($service['faq'])): ?>
<section class="ec-section ec-section--tight">
  <div class="ec-shell ec-faq__wrap">
    <h2 data-split>Common questions</h2>
    <div class="ec-acc ec-faq" data-reveal>
      <?php foreach ($service['faq'] as $qa): ?>
        <details class="ec-acc__item">
          <summary><?= esc($qa[0]) ?></summary>
          <div class="ec-acc__body"><p><?= esc($qa[1]) ?></p></div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="ec-section ec-dark">
  <div class="ec-shell">
    <h2 data-split style="margin-bottom:clamp(2rem,4vw,3rem)">What else we build</h2>
    <ul class="ec-index">
      <?php foreach ($related as $rSlug => $rSvc): ?>
        <li data-reveal>
          <a href="<?= base_url('services/' . $rSlug) ?>">
            <h3><?= esc($rSvc['name']) ?></h3>
            <p><?= esc($rSvc['lead']) ?></p>
            <span aria-hidden="true"></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
    <div style="margin-top:clamp(2.5rem,5vw,3.5rem)">
      <a class="ec-btn ec-btn--primary" href="<?= base_url() ?>#contact">Start a project</a>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
