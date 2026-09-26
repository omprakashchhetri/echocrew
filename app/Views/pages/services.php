<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="ec-subhero" data-scope>
  <img class="ec-subhero__mark" src="<?= base_url('assets/img/ec/echocrew-mark.png') ?>" alt="" width="288" height="400" aria-hidden="true" data-parallax="70">
  <div class="ec-shell">
    <nav class="ec-crumbs" aria-label="Breadcrumb"><a href="<?= base_url() ?>">Home</a><i aria-hidden="true">/</i><span aria-current="page">Services</span></nav>
    <h1 data-split>Software development services</h1>
    <p class="ec-lead" data-reveal>
      Custom software, websites, CRM, e-commerce, integrations, automation, AI and SEO, built for
      businesses in Siliguri, across North Bengal and beyond.
    </p>
  </div>
</section>

<div class="ec-band">
  <figure class="ec-media" data-unveil>
    <img src="<?= base_url('assets/img/ec/team-office.webp') ?>" alt="A product team working together at their desks" width="1600" height="1068">
  </figure>
</div>

<section class="ec-section">
  <div class="ec-shell">
    <ol class="ec-index">
      <?php foreach ($services as $slug => $svc): ?>
        <li data-reveal>
          <a href="<?= base_url('services/' . $slug) ?>">
            <h2><?= esc($svc['name']) ?></h2>
            <p><?= esc($svc['lead']) ?></p>
            <span aria-hidden="true"></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<section class="ec-section ec-dark ec-cta">
  <div class="ec-shell ec-cta__in">
    <h2 data-split>Have a business problem we can build a solution for?</h2>
    <a class="ec-btn ec-btn--primary" href="<?= base_url() ?>#contact">Start a project</a>
  </div>
</section>

<?= $this->endSection() ?>
