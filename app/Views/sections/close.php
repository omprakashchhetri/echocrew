<?php
/** @var \Config\Seo $ecSeo */
$ecSeo = config('Seo');
?>
<section class="ec-section" id="about">
  <div class="ec-shell">
    <div class="ec-about__grid">
      <div class="ec-about__copy">
        <p class="ec-eyebrow">About</p>
        <h2 data-split>We build technology around how businesses actually work.</h2>
        <p class="ec-lead" data-reveal>
          EchoCrew works across both modern and legacy technology. That range matters: it means the
          recommendation isn't decided by the only stack we know.
        </p>
        <p data-reveal>
          The right solution is not always a new application. Sometimes it is a smarter version of
          what you already have.
        </p>
      </div>

      <div class="ec-about__art">
        <figure class="ec-media" data-unveil>
          <img src="<?= base_url('assets/img/ec/pair-review.webp') ?>" alt="Two people reviewing work on a laptop" width="950" height="960" loading="lazy">
        </figure>
        <figure class="ec-media" data-parallax="-40">
          <img src="<?= base_url('assets/img/ec/at-the-desk.webp') ?>" alt="A developer smiling at her desk" width="670" height="877" loading="lazy">
        </figure>
      </div>
    </div>

    <ul class="ec-caps">
      <?php foreach ([
          ['Build', 'New systems from a blank repository.'],
          ['Customise', 'Reshape software you already own.'],
          ['Connect', 'Integrate systems that ignore each other.'],
          ['Automate', 'Retire the repetitive manual steps.'],
          ['Modernise', 'Bring legacy applications forward in stages.'],
          ['Integrate AI', 'Where it earns its place in the workflow.'],
          ['Improve', 'Strengthen digital products already running.'],
          ['Grow', 'Marketing and optimisation on top of the build.'],
      ] as [$verb, $line]): ?>
        <li data-reveal><b><?= esc($verb) ?></b><span><?= esc($line) ?></span></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="ec-section ec-section--tight" id="faq">
  <div class="ec-shell ec-faq__wrap">
    <h2 data-split>Before you get in touch</h2>
    <div class="ec-acc ec-faq" data-reveal>
      <?php foreach ($ecSeo->homeFaq as $faq): ?>
        <details class="ec-acc__item">
          <summary><?= esc($faq[0]) ?></summary>
          <div class="ec-acc__body"><p><?= esc($faq[1]) ?></p></div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="ec-section ec-dark ec-contact" id="contact">
  <img class="ec-contact__mark" src="<?= base_url('assets/img/ec/echocrew-mark.png') ?>" alt="" width="288" height="400" aria-hidden="true" loading="lazy" data-parallax="60">
  <div class="ec-shell ec-contact__grid">
    <div class="ec-contact__intro">
      <h2 data-split>Have a business problem we can build a solution for?</h2>
      <p class="ec-lead" data-reveal>
        Tell us what you are trying to achieve, what currently isn't working, or what you want to
        automate. The more specific the problem, the more useful the first reply.
      </p>
      <dl class="ec-contact__facts" data-reveal>
        <div><dt>Based in</dt><dd><?= esc($ecSeo->nap['locality']) ?>, <?= esc($ecSeo->nap['region']) ?></dd></div>
        <div><dt>Hours</dt><dd><?= esc($ecSeo->openingHours['days'][0]) ?> to <?= esc(end($ecSeo->openingHours['days'])) ?>, <?= esc($ecSeo->openingHours['opens']) ?> to <?= esc($ecSeo->openingHours['closes']) ?></dd></div>
        <?php if ($ecSeo->nap['email'] !== ''): ?>
          <div><dt>Email</dt><dd><a href="mailto:<?= esc($ecSeo->nap['email'], 'attr') ?>"><?= esc($ecSeo->nap['email']) ?></a></dd></div>
        <?php endif; ?>
        <?php if ($ecSeo->nap['telephone'] !== ''): ?>
          <div><dt>Phone</dt><dd><a href="tel:<?= esc(preg_replace('/\s+/', '', $ecSeo->nap['telephone']), 'attr') ?>"><?= esc($ecSeo->nap['telephone']) ?></a></dd></div>
        <?php endif; ?>
      </dl>
    </div>

    <?php
    $errors = session()->getFlashdata('ec_errors') ?? [];
    $old    = static fn (string $k): string => (string) (old($k) ?? '');
    $bad    = static fn (string $k): string => isset($errors[$k]) ? ' aria-invalid="true" aria-describedby="ec-' . $k . '-err"' : '';
    $err    = static fn (string $k): string => isset($errors[$k]) ? '<span class="ec-err" id="ec-' . $k . '-err">' . esc($errors[$k]) . '</span>' : '';
    ?>

    <form class="ec-form" method="post" action="<?= site_url('enquiry') ?>" data-enquiry novalidate data-reveal>
      <?= csrf_field() ?>

      <?php if (session()->getFlashdata('ec_success')): ?>
        <div class="ec-alert ec-alert--ok" data-scroll-to role="status"><?= esc(session()->getFlashdata('ec_success')) ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('ec_error')): ?>
        <div class="ec-alert ec-alert--bad" data-scroll-to role="alert"><?= esc(session()->getFlashdata('ec_error')) ?></div>
      <?php endif; ?>

      <div class="ec-hp" aria-hidden="true">
        <label for="ec-website">Leave this empty</label>
        <input type="text" id="ec-website" name="ec_website" tabindex="-1" autocomplete="off">
      </div>

      <div class="ec-field">
        <label for="ec-name">Name</label>
        <input type="text" id="ec-name" name="name" autocomplete="name" value="<?= esc($old('name')) ?>" required<?= $bad('name') ?>>
        <?= $err('name') ?>
      </div>

      <div class="ec-field">
        <label for="ec-company">Company <small>(optional)</small></label>
        <input type="text" id="ec-company" name="company" autocomplete="organization" value="<?= esc($old('company')) ?>"<?= $bad('company') ?>>
        <?= $err('company') ?>
      </div>

      <div class="ec-field">
        <label for="ec-email">Email</label>
        <input type="email" id="ec-email" name="email" autocomplete="email" value="<?= esc($old('email')) ?>" required<?= $bad('email') ?>>
        <?= $err('email') ?>
      </div>

      <div class="ec-field">
        <label for="ec-phone">Phone <small>(optional)</small></label>
        <input type="tel" id="ec-phone" name="phone" autocomplete="tel" value="<?= esc($old('phone')) ?>"<?= $bad('phone') ?>>
        <?= $err('phone') ?>
      </div>

      <div class="ec-field">
        <label for="ec-service">Service</label>
        <select id="ec-service" name="service"<?= $bad('service') ?>>
          <option value="">Not sure yet</option>
          <?php foreach ([
              'Custom software', 'Website development', 'CRM development', 'Management software', 'E-commerce',
              'API integration', 'Automation', 'AI solution', 'Legacy system modernisation',
              'Digital marketing', 'Other',
          ] as $service): ?>
            <option value="<?= esc($service, 'attr') ?>" <?= $old('service') === $service ? 'selected' : '' ?>><?= esc($service) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="ec-field">
        <label for="ec-current">Current website or system <small>(optional)</small></label>
        <input type="text" id="ec-current" name="current_system" placeholder="URL, or the software you use today" value="<?= esc($old('current_system')) ?>"<?= $bad('current_system') ?>>
        <?= $err('current_system') ?>
      </div>

      <div class="ec-field">
        <label for="ec-budget">Budget <small>(optional)</small></label>
        <input type="text" id="ec-budget" name="budget" placeholder="A range is fine" value="<?= esc($old('budget')) ?>"<?= $bad('budget') ?>>
        <?= $err('budget') ?>
      </div>

      <div class="ec-field">
        <label for="ec-timeline">Timeline <small>(optional)</small></label>
        <input type="text" id="ec-timeline" name="timeline" placeholder="When you want it live" value="<?= esc($old('timeline')) ?>"<?= $bad('timeline') ?>>
        <?= $err('timeline') ?>
      </div>

      <div class="ec-field ec-field--full">
        <label for="ec-prefer">Preferred contact method</label>
        <select id="ec-prefer" name="preferred_contact">
          <?php foreach (['Email', 'Phone', 'WhatsApp'] as $method): ?>
            <option value="<?= $method ?>" <?= $old('preferred_contact') === $method ? 'selected' : '' ?>><?= $method ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="ec-field ec-field--full">
        <label for="ec-message">Project description</label>
        <textarea id="ec-message" name="message" required aria-describedby="ec-message-help<?= isset($errors['message']) ? ' ec-message-err' : '' ?>"<?= isset($errors['message']) ? ' aria-invalid="true"' : '' ?> placeholder="What are you trying to achieve, and what isn't working today?"><?= esc($old('message')) ?></textarea>
        <span class="ec-help" id="ec-message-help">At least 20 characters. A couple of sentences is plenty.</span>
        <?= $err('message') ?>
      </div>

      <?= (new \App\Libraries\Captcha())->render('enquiry') ?>

      <div class="ec-form__foot">
        <button class="ec-btn ec-btn--primary" type="submit">Send enquiry</button>
        <span class="ec-help">We read every enquiry and reply on the contact method you choose.</span>
      </div>
    </form>
  </div>
</section>
