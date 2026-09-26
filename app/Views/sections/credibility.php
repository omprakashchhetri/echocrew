<section class="ec-section">
  <div class="ec-shell ec-why__grid">
    <figure class="ec-media ec-why__photo" data-unveil>
      <img src="<?= base_url('assets/img/ec/team-discussion.webp') ?>" alt="Colleagues talking through a problem around a laptop" width="674" height="450" loading="lazy">
    </figure>

    <div>
      <h2 data-split>Built around your business.</h2>
      <div class="ec-why__list">
        <?php foreach ([
            ['Business first', 'We solve the business problem first and choose the technology second. That order rarely gets reversed without cost.'],
            ['Custom by design', 'Every system starts from your requirements, not from a template that has to be argued into shape.'],
            ['Integration ready', 'Systems are built expecting company: APIs, webhooks and clean data contracts from the start.'],
            ['Automation focused', 'If a person is doing it the same way every day, it is a candidate for a workflow.'],
            ['Built to evolve', 'Your process will change. The system is structured so that change is an edit, not a rebuild.'],
        ] as [$h, $p]): ?>
          <div data-reveal>
            <h3><?= esc($h) ?></h3>
            <p><?= esc($p) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<section class="ec-section" id="technologies" style="padding-top:0">
  <div class="ec-shell">
    <div class="ec-tech__head">
      <h2 data-split>We choose technology based on the problem.</h2>
      <p class="ec-lead" data-reveal>
        Technology is a tool, and the business problem comes first. These are tools we reach for
        often, not the limits of what we work with.
      </p>
    </div>

    <ul class="ec-keys" data-keys>
      <?php
      $ecAccentKeys = ['PHP', 'Laravel', 'Vue.js'];
      $ecWideKeys   = ['REST APIs', 'Automation platforms'];
      foreach ([
          'WordPress', 'PHP', 'Laravel', 'CodeIgniter', 'Vue.js', 'JavaScript', 'jQuery',
          'Bootstrap', 'Tailwind CSS', 'MySQL', 'REST APIs', 'Webhooks', 'Git', 'GitHub',
          'Docker', 'AI APIs', 'Automation platforms',
      ] as $tech):
          $cls = 'ec-key' . (in_array($tech, $ecAccentKeys, true) ? ' ec-key--accent' : '') . (in_array($tech, $ecWideKeys, true) ? ' ec-key--wide' : '');
      ?>
        <li class="<?= $cls ?>"><?= esc($tech) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="ec-process ec-dark" id="process" data-pan>
  <div class="ec-shell ec-process__head">
    <h2 data-split>From idea to working system.</h2>
  </div>

  <ol class="ec-process__track">
    <?php foreach ([
        ['Discover', 'Understand the business, the people and where the current process breaks down.'],
        ['Plan', 'Define scope, architecture, data model and what success looks like.'],
        ['Design', 'Shape the interface around the tasks people perform most often.'],
        ['Develop', 'Build the solution in reviewable increments you can see running.'],
        ['Integrate', 'Connect external systems, APIs and existing databases.'],
        ['Automate', 'Remove the repeated manual steps the process no longer needs.'],
        ['Launch', 'Deploy, test with real data and hand over with documentation.'],
        ['Improve', 'Iterate as the business changes and the system grows with it.'],
    ] as $i => [$h, $p]): ?>
      <li class="ec-step">
        <span class="ec-step__n" aria-hidden="true"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
        <div>
          <h3><?= esc($h) ?></h3>
          <p><?= esc($p) ?></p>
        </div>
      </li>
    <?php endforeach; ?>
  </ol>
</section>
