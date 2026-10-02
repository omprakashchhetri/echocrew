<?php
/** @var bool $turnstile @var string $siteKey @var string $form @var string|null $question */
?>
<?php if ($turnstile): ?>
  <div class="ec-field ec-field--full ec-captcha">
    <div class="cf-turnstile" data-sitekey="<?= esc($siteKey, 'attr') ?>" data-theme="light"></div>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  </div>
<?php else: ?>
  <div class="ec-field ec-field--full ec-captcha">
    <label for="ec-captcha-<?= esc($form, 'attr') ?>">Security check: what is <?= esc($question) ?>?</label>
    <input type="text" id="ec-captcha-<?= esc($form, 'attr') ?>" name="ec_captcha" inputmode="numeric" pattern="[0-9]*" autocomplete="off" required style="max-width:140px">
  </div>
<?php endif; ?>
