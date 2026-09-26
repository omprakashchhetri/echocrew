<section class="ec-section ec-manifesto" id="solutions">
  <div class="ec-shell">
    <p class="ec-eyebrow">How we work</p>
    <h2 class="ec-manifesto__text" data-fill>Technology should adapt to your business. <em>Not the other way around.</em></h2>

    <div class="ec-manifesto__foot">
      <p class="ec-lead" data-reveal>
        Generic software asks you to change your process to fit the product. We start from the
        workflow you already run: who does what, where it stalls, what gets retyped. Then we build
        the system around it.
      </p>
      <ol class="ec-flow" data-flow>
        <?php foreach (['Understand', 'Design', 'Develop', 'Integrate', 'Automate', 'Scale'] as $step): ?>
          <li><?= $step ?></li>
        <?php endforeach; ?>
      </ol>
    </div>
  </div>
</section>

<section class="ec-section" id="services" style="padding-top:0">
  <?php
  // Sentence-case a service name without flattening acronyms (CRM, API, AI, SEO).
  $ecLower = static fn (string $s): string => preg_replace_callback('/\p{L}+/u', static fn ($m) => (strlen($m[0]) > 1 && strtoupper($m[0]) === $m[0]) ? $m[0] : strtolower($m[0]), $s);
  $ecServices = [
      ['web-development', 'Custom website development', 'Corporate sites, business sites, landing pages, portals and full web applications, built rather than assembled from a theme.', ['Performance and Core Web Vitals', 'UX designed for the visitor journey', 'Technical SEO from the first line', 'Conversion-focused structure', 'Custom functionality, not plugin stacks']],
      ['crm-development', 'CRM development', 'Build a CRM from scratch or reshape an existing one until it matches how your team actually sells and supports.', ['Lead capture and management', 'Customer records and history', 'Sales pipelines and stages', 'Reporting and dashboards', 'Workflow automation', 'Communication and integrations']],
      ['school-management-software', 'Business management software', 'Custom operational software for organisations that have outgrown spreadsheets and off-the-shelf tools.', ['School and education management', 'Internal operations systems', 'Data and records management', 'Role-based access', 'Reporting systems', 'Approval and workflow chains']],
      ['ecommerce-development', 'E-commerce development', 'Custom commerce systems built around your catalogue, your fulfilment and your margins.', ['Products and variants', 'Orders and customers', 'Payments and checkout', 'Inventory control', 'Reporting', 'Integrations and automation']],
      ['api-integration', 'API and system integrations', 'Make the tools you already pay for talk to each other, in both directions.', ['CRM and websites', 'Payment gateways', 'Messaging and email platforms', 'Databases and internal apps', 'Third-party and partner APIs', 'Webhooks and scheduled syncs']],
      ['business-automation', 'Automation', 'Every process a person repeats daily is a process software can run instead.', ['Lead routing and assignment', 'Notifications and alerts', 'Data synchronisation between systems', 'Scheduled reports', 'Customer communication sequences', 'CRM and API workflows']],
      ['ai-solutions', 'AI solutions', 'AI added to a workflow that benefits from it, not bolted onto the homepage as a badge.', ['Support and internal assistants', 'Lead qualification', 'Content and document processing', 'Data analysis and summarisation', 'Automated responses', 'Intelligent routing inside existing automation']],
      ['legacy-system-modernization', 'Legacy system modernisation', 'Keep the business logic that took years to get right. Replace only what is holding it back.', ['Customisation of existing PHP and CRM applications', 'API layers over older systems', 'Modern interfaces on proven backends', 'Incremental migration instead of rewrites', 'Performance and security hardening']],
      ['digital-marketing-seo', 'Digital marketing', 'Development and growth work better when the build and the campaign understand each other.', ['SEO and technical SEO', 'Landing pages', 'Conversion optimisation', 'Analytics and tracking', 'Content strategy', 'Digital campaigns']],
  ];
  ?>
  <div class="ec-shell ec-svc">
    <div class="ec-svc__aside">
      <h2 data-split>What we build</h2>
      <p class="ec-muted">Open any capability to see what it covers in practice.</p>
      <a class="ec-link" href="<?= base_url('services') ?>">All services</a>
      <figure class="ec-media ec-svc__photo" data-unveil>
        <img src="<?= base_url('assets/img/ec/planning-wall.webp') ?>" alt="A team planning work with sticky notes on a board" width="510" height="562" loading="lazy">
      </figure>
    </div>

    <div class="ec-acc" data-reveal>
      <?php foreach ($ecServices as $i => [$slug, $name, $desc, $points]): ?>
        <details class="ec-acc__item" name="ec-services"<?= $i === 0 ? ' open' : '' ?>>
          <summary><?= esc($name) ?></summary>
          <div class="ec-acc__body">
            <p><?= esc($desc) ?></p>
            <ul class="ec-checks">
              <?php foreach ($points as $point): ?><li><?= esc($point) ?></li><?php endforeach; ?>
            </ul>
            <div><a class="ec-link" href="<?= base_url('services/' . $slug) ?>">More on <?= esc($ecLower($name)) ?></a></div>
          </div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
