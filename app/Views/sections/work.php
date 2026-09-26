<section class="ec-section" id="work">
  <div class="ec-shell">
    <div class="ec-work__head">
      <h2 data-split>Some things we've built</h2>
      <p class="ec-lead" data-reveal>
        Products and systems built for real operational use: commerce, administration,
        communication and money movement.
      </p>
    </div>

    <?php
    // Covers are typographic on purpose. Swap in real product screenshots when they are
    // available by replacing .ec-work__cover with an <figure class="ec-media"> + <img>.
    $ecWork = [
        ['name' => 'EchoCart', 'cover' => 'EchoCart', 'tone' => 'deep', 'wide' => true,
         'kind' => 'E-commerce platform',
         'desc' => 'A custom e-commerce system for running online commerce end to end: catalogue, customers, orders and the workflows that connect them.',
         'tech' => ['Laravel', 'Vue', 'PHP', 'MySQL', 'APIs']],
        ['name' => 'School management software', 'cover' => 'School', 'tone' => 'lav',
         'kind' => 'Education management system',
         'desc' => 'Management software shaped around school administration: records, staff and the operational routines that run a term.',
         'tech' => ['PHP', 'CodeIgniter', 'MySQL', 'Bootstrap']],
        ['name' => 'WhatsAppSend AI', 'cover' => 'WhatsAppSend', 'tone' => 'mint',
         'kind' => 'AI, communication and automation',
         'desc' => 'A communication system built around modern messaging, with AI and automation handling the repetitive side of business conversations.',
         'tech' => ['PHP', 'APIs', 'AI', 'Automation']],
        ['name' => 'Money Tracker', 'cover' => 'Money Tracker', 'tone' => 'peach',
         'kind' => 'Financial management',
         'desc' => 'A practical interface for organising and tracking financial information, built to stay fast as records accumulate.',
         'tech' => ['PHP', 'MySQL', 'JavaScript']],
        ['name' => 'Custom client websites', 'cover' => 'Websites', 'tone' => 'sky',
         'kind' => 'Website development',
         'desc' => 'Purpose-built websites for businesses with different branding, functional and operational requirements.',
         'tech' => ['PHP', 'WordPress', 'JavaScript', 'SEO']],
        ['name' => 'Legacy system customisation', 'cover' => 'Legacy', 'tone' => 'teal', 'wide' => true,
         'kind' => 'Modernisation',
         'desc' => 'Existing systems customised, integrated and modernised where a rebuild would have cost more than it returned.',
         'tech' => ['PHP', 'CodeIgniter', 'REST APIs', 'MySQL']],
    ];
    ?>

    <div class="ec-work">
      <?php foreach ($ecWork as $project): ?>
        <article class="ec-work__item<?= ! empty($project['wide']) ? ' ec-work__item--wide' : '' ?>" data-reveal>
          <div class="ec-work__cover" data-tone="<?= $project['tone'] ?>" aria-hidden="true">
            <span class="ec-work__ribbon"></span>
            <p class="ec-echo"><span><?= esc($project['cover']) ?></span><span><?= esc($project['cover']) ?></span><span><?= esc($project['cover']) ?></span></p>
          </div>
          <div class="ec-work__body">
            <span class="ec-work__kind"><?= esc($project['kind']) ?></span>
            <h3><?= esc($project['name']) ?></h3>
            <p><?= esc($project['desc']) ?></p>
            <ul class="ec-tags" aria-label="Built with">
              <?php foreach ($project['tech'] as $tech): ?><li><?= esc($tech) ?></li><?php endforeach; ?>
            </ul>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="ec-section ec-dark ec-prob" id="problems">
  <div class="ec-shell">
    <div class="ec-prob__head">
      <h2 data-split>Bring us the problem.</h2>
      <p class="ec-lead" data-reveal>Pick the sentence closest to yours.</p>
    </div>

    <?php
    $ecProblems = [
        ['We\'re managing everything in spreadsheets.', 'Build a custom management system', 'Spreadsheets stop scaling the moment more than one person edits them. We replace the file with a system that holds the same data with roles, validation, history and reporting, so nobody is emailing version 7 of the sheet.'],
        ['Our CRM doesn\'t match our workflow.', 'Customise or build the CRM', 'If your team maintains a side spreadsheet next to the CRM, the CRM is wrong. We either reshape the one you own or build one that follows your actual pipeline, stages and handoffs.'],
        ['Our systems don\'t talk to each other.', 'Build the integration layer', 'Website, CRM, accounts, messaging: each holding a slightly different version of the truth. We connect them with APIs and webhooks so data is entered once and appears everywhere.'],
        ['Our team repeats the same tasks every day.', 'Automate the workflow', 'Copying records, sending the same follow-up, rebuilding the same report. We map the repeated steps and move them into automated workflows with alerts when something needs a human.'],
        ['Our old software works, but it is hard to maintain.', 'Modernise it incrementally', 'Working software is an asset. We modernise in stages (an API layer, then a modern interface, then automation) keeping the business logic that already earns its place.'],
        ['We need a website that does more than display information.', 'Build a web application', 'Portals, dashboards, quoting tools, booking flows, client areas. The site stops being a brochure and starts doing operational work.'],
    ];
    ?>

    <div class="ec-prob__grid" data-tabs>
      <ul class="ec-notes" role="tablist" aria-label="Common problems" data-reveal>
        <?php foreach ($ecProblems as $i => $problem): ?>
          <li role="presentation">
            <button class="ec-note-btn" type="button" role="tab" id="ec-prob-tab-<?= $i ?>"
                    aria-controls="ec-prob-panel-<?= $i ?>"
                    aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                    tabindex="<?= $i === 0 ? '0' : '-1' ?>"><?= esc($problem[0]) ?></button>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="ec-prob__side">
        <?php foreach ($ecProblems as $i => $problem): ?>
          <div class="ec-prob__panel" role="tabpanel" id="ec-prob-panel-<?= $i ?>" aria-labelledby="ec-prob-tab-<?= $i ?>" tabindex="0"<?= $i === 0 ? '' : ' hidden' ?>>
            <p class="ec-eyebrow">What we'd build</p>
            <h3><?= esc($problem[1]) ?></h3>
            <p><?= esc($problem[2]) ?></p>
            <a class="ec-btn ec-btn--primary" href="#contact">Start a project</a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
