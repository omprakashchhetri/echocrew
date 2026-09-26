<?php
/** @var \Config\Seo $ecSeo */
$ecSeo  = config('Seo');
$ecHome = base_url();
$ecNap  = $ecSeo->nap;
?>
<footer class="ec-footer">
  <div class="ec-shell">
    <p class="ec-footer__big" data-reveal>You bring the problem. <span>We build the system.</span></p>

    <div class="ec-footer__cols">
      <div class="ec-footer__brand">
        <img src="<?= base_url('assets/img/ec/echocrew-logo-light.png') ?>" alt="EchoCrew" width="353" height="96" loading="lazy">
        <p>Custom software and digital systems built around the way your business already works.</p>
      </div>

      <div>
        <h4>Services</h4>
        <ul>
          <li><a href="<?= base_url('services/custom-software-development') ?>">Custom software</a></li>
          <li><a href="<?= base_url('services/web-development') ?>">Website development</a></li>
          <li><a href="<?= base_url('services/crm-development') ?>">CRM development</a></li>
          <li><a href="<?= base_url('services/school-management-software') ?>">Management software</a></li>
          <li><a href="<?= base_url('services/ecommerce-development') ?>">E-commerce</a></li>
          <li><a href="<?= base_url('services/api-integration') ?>">Integrations</a></li>
          <li><a href="<?= base_url('services/business-automation') ?>">Automation</a></li>
          <li><a href="<?= base_url('services/ai-solutions') ?>">AI solutions</a></li>
          <li><a href="<?= base_url('services/legacy-system-modernization') ?>">Legacy modernisation</a></li>
          <li><a href="<?= base_url('services/digital-marketing-seo') ?>">Digital marketing</a></li>
        </ul>
      </div>

      <div>
        <h4>Company</h4>
        <ul>
          <li><a href="<?= $ecHome ?>#about">About</a></li>
          <li><a href="<?= $ecHome ?>#work">Work</a></li>
          <li><a href="<?= $ecHome ?>#technologies">Technologies</a></li>
          <li><a href="<?= $ecHome ?>#faq">Questions</a></li>
          <li><a href="<?= base_url('blog') ?>">Blog</a></li>
          <li><a href="<?= $ecHome ?>#contact">Contact</a></li>
        </ul>
      </div>

      <div>
        <h4>Where we work</h4>
        <ul>
          <li><?= esc($ecNap['locality']) ?>, <?= esc($ecNap['region']) ?></li>
          <li>Projects across North Bengal, Sikkim and the rest of India</li>
          <li><?= esc($ecSeo->openingHours['days'][0]) ?> to <?= esc(end($ecSeo->openingHours['days'])) ?>, <?= esc($ecSeo->openingHours['opens']) ?> to <?= esc($ecSeo->openingHours['closes']) ?></li>
          <?php if ($ecNap['email'] !== ''): ?><li><a href="mailto:<?= esc($ecNap['email'], 'attr') ?>"><?= esc($ecNap['email']) ?></a></li><?php endif; ?>
          <?php if ($ecNap['telephone'] !== ''): ?><li><a href="tel:<?= esc(preg_replace('/\s+/', '', $ecNap['telephone']), 'attr') ?>"><?= esc($ecNap['telephone']) ?></a></li><?php endif; ?>
        </ul>
      </div>
    </div>

    <div class="ec-footer__bar">
      <span>&copy; <?= date('Y') ?> EchoCrew. All rights reserved.</span>
      <span>Code that echoes.</span>
    </div>
  </div>
</footer>
