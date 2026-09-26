<?php
// Words are wrapped server-side so the entrance animation never depends on line breaks.
$ecHeroWords = ['We', 'build', 'digital', 'systems', 'that', 'move', 'businesses'];
?>
<section class="ec-hero" data-scope>
  <div class="ec-shell ec-hero__grid">
    <div>
      <h1 class="ec-hero__title">
        <?php foreach ($ecHeroWords as $word): ?><span class="ec-w"><?= $word ?></span> <?php endforeach; ?><span class="ec-w ec-scribble">forward.<svg viewBox="0 0 300 24" preserveAspectRatio="none" aria-hidden="true"><path d="M4 15 C 48 5, 96 21, 150 12 S 246 4, 296 13"/></svg></span>
      </h1>

      <p class="ec-hero__lead" data-reveal>
        Websites, CRMs, management software, integrations and automation, built around the way your
        business already works.
      </p>

      <div class="ec-hero__actions" data-reveal>
        <a class="ec-btn ec-btn--primary" href="#contact">Start a project</a>
        <a class="ec-link" href="#work">See our work</a>
      </div>
    </div>

    <div class="ec-hero__art">
      <figure class="ec-hero__photo ec-hero__photo--main" data-parallax="-26">
        <img src="<?= base_url('assets/img/ec/team-office.webp') ?>" alt="A product team working together at their desks" width="1600" height="1068" fetchpriority="high">
      </figure>
      <figure class="ec-hero__photo ec-hero__photo--small" data-parallax="42">
        <img src="<?= base_url('assets/img/ec/whiteboard-session.webp') ?>" alt="A team working through a plan on a whiteboard" width="560" height="360" loading="lazy">
      </figure>
      <p class="ec-note" data-parallax="18">Custom-built. Business-focused. Designed to evolve.</p>
      <img class="ec-hero__mark" src="<?= base_url('assets/img/ec/echocrew-mark.png') ?>" alt="" width="288" height="400" aria-hidden="true" data-parallax="90">
    </div>
  </div>
</section>

<section class="ec-ticker" aria-hidden="true">
  <?php $ecTickA = ['Custom development', 'CRM', 'Business software', 'E-commerce', 'API integration', 'Automation', 'AI', 'Digital marketing']; ?>
  <?php $ecTickB = ['Website', 'Application', 'CRM', 'API', 'Automation', 'AI', 'Business']; ?>
  <div class="ec-ticker__row" data-ticker="-1">
    <?php for ($r = 0; $r < 2; $r++): foreach ($ecTickA as $t): ?><span><?= $t ?></span><i class="ec-ticker__sep"></i><?php endforeach; endfor; ?>
  </div>
  <div class="ec-ticker__row ec-ticker__row--outline" data-ticker="1">
    <?php for ($r = 0; $r < 2; $r++): foreach ($ecTickB as $t): ?><span><?= $t ?></span><i class="ec-ticker__sep"></i><?php endforeach; endfor; ?>
  </div>
</section>
