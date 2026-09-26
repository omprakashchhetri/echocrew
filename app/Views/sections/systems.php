<section class="ec-section ec-dark" id="legacy">
  <div class="ec-shell ec-legacy__grid">
    <div class="ec-legacy__copy">
      <h2 data-split>Your legacy system isn't always the problem.</h2>
      <p class="ec-lead" data-reveal>
        Years of business logic often live inside older applications. Replacing everything can be
        expensive, risky and unnecessary.
      </p>
      <p data-reveal>
        EchoCrew customises, integrates, optimises and modernises existing PHP, CRM and legacy
        applications while preserving the logic that already works.
      </p>
      <div data-reveal><a class="ec-link" href="<?= base_url('services/legacy-system-modernization') ?>">How we modernise in stages</a></div>
    </div>

    <ol class="ec-strata" data-strata aria-label="Modernisation layers, from the kept system upward">
      <?php foreach ([
          ['Legacy system', 'the logic you keep'],
          ['API layer', 'a way in and out'],
          ['Modern UI', 'the part people touch'],
          ['Automation', 'the manual steps removed'],
          ['Analytics', 'what the data now shows'],
      ] as $node): ?>
        <li><b><?= esc($node[0]) ?></b><span><?= esc($node[1]) ?></span></li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<section class="ec-section" id="automation">
  <div class="ec-shell">
    <div class="ec-auto__head">
      <h2 data-split>Turn repetitive work into automated workflows.</h2>
      <p class="ec-lead" data-reveal>
        A lead arrives on the site, reaches the CRM, triggers a message, updates a record and lands
        in a report. Nobody retypes it.
      </p>
    </div>

    <ol class="ec-chain" data-chain aria-label="A typical automation chain">
      <?php foreach (['Website', 'CRM', 'API', 'Database', 'WhatsApp', 'Email', 'Automation', 'Reports'] as $node): ?>
        <li><span><?= $node ?></span></li>
      <?php endforeach; ?>
    </ol>

    <div class="ec-auto__list" data-reveal>
      <h3>What gets automated</h3>
      <ul class="ec-checks">
        <?php foreach ([
            'Lead creation from every form and channel',
            'Customer communication at the right moment',
            'Internal notifications and escalations',
            'Data synchronisation between systems',
            'Scheduled and triggered reports',
            'CRM record updates and stage changes',
        ] as $item): ?>
          <li><?= esc($item) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<section class="ec-section ec-ai" id="ai">
  <div class="ec-shell">
    <div class="ec-ai__head">
      <h2 data-split>Add intelligence where it actually matters.</h2>
      <p class="ec-lead" data-reveal>
        AI belongs inside a workflow it measurably improves. We add it where it reduces handling time
        or removes a manual reading task, and leave it out where a rule is cheaper and more reliable.
      </p>
    </div>

    <div class="ec-bento">
      <?php foreach ([
          ['Customer support', 'Answer repeat questions from your own content, hand over when it matters.', 'Support', -30],
          ['Lead qualification', 'Score and route enquiries before a person opens them.', 'Leads', 30],
          ['Document processing', 'Read incoming documents and turn them into structured records.', 'Docs', -20],
          ['Data analysis', 'Summarise what changed across your systems this week.', 'Data', 16],
      ] as [$h, $p, $glyph, $depth]): ?>
        <article class="ec-bento__cell" data-reveal>
          <span class="ec-bento__glyph" aria-hidden="true" data-parallax="<?= $depth ?>"><?= $glyph ?></span>
          <h3><?= esc($h) ?></h3>
          <p><?= esc($p) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="ec-section ec-grow" id="marketing">
  <div class="ec-shell">
    <h2 class="ec-grow__words">
      <span data-drift="-60">Build it.</span>
      <span data-drift="40">Launch it.</span>
      <span data-drift="-30"><span class="ec-ring">Grow it.<svg viewBox="0 0 310 95" preserveAspectRatio="none" aria-hidden="true"><path d="M26 34 C 40 10, 140 2, 214 8 C 282 14, 306 36, 296 58 C 282 86, 156 94, 76 82 C 30 75, 8 56, 22 36 C 30 26, 48 18, 70 14"/></svg></span></span>
    </h2>

    <div class="ec-grow__foot">
      <p class="ec-lead" data-reveal>
        Development and digital growth work better when the technology and the marketing strategy
        understand each other: the same team owning site speed, structure and what converts.
      </p>
      <ul class="ec-chips" data-reveal>
        <?php foreach (['SEO', 'Technical SEO', 'Analytics', 'Landing pages', 'Conversion optimisation', 'Content strategy', 'Digital campaigns'] as $item): ?>
          <li><?= esc($item) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<section class="ec-section ec-section--tight ec-ind" id="industries">
  <div class="ec-shell ec-ind__wrap">
    <h2>Different businesses. Different systems.</h2>
    <ul class="ec-ind__list" data-reveal>
      <?php foreach (['Education', 'E-commerce', 'Professional services', 'Healthcare', 'Retail', 'Finance', 'Startups', 'Small and medium business', 'Internal operations'] as $item): ?>
        <li><?= esc($item) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
