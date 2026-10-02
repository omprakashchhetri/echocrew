<?php

namespace App\Libraries;

use CodeIgniter\HTTP\IncomingRequest;

/**
 * Bot challenge for public forms.
 *
 * - Cloudflare Turnstile when `turnstile.siteKey` and `turnstile.secretKey` are set in .env
 *   (recommended in production: free, privacy-friendly, no puzzles for most people).
 * - Otherwise a self-hosted fallback: a one-time arithmetic question plus a minimum
 *   fill-in time, both tracked server-side in the session.
 *
 * Always combine with the Throttle filter and the honeypot field.
 */
class Captcha
{
    private const MIN_SECONDS = 3;
    private const MAX_SECONDS = 3600;

    public function usesTurnstile(): bool
    {
        return $this->siteKey() !== '' && $this->secretKey() !== '';
    }

    public function siteKey(): string
    {
        return (string) env('turnstile.siteKey', '');
    }

    private function secretKey(): string
    {
        return (string) env('turnstile.secretKey', '');
    }

    /** HTML for the form. Issues a fresh built-in challenge each time it renders. */
    public function render(string $form): string
    {
        $data = ['turnstile' => $this->usesTurnstile(), 'siteKey' => $this->siteKey(), 'form' => $form];

        if (! $data['turnstile']) {
            $a = random_int(2, 9);
            $b = random_int(1, 9);
            session()->set('captcha_' . $form, ['answer' => $a + $b, 'issued' => time()]);
            $data['question'] = "{$a} + {$b}";
        }

        return view('partials/captcha', $data);
    }

    /** @return string|null error message, or null when the challenge passed */
    public function verify(IncomingRequest $request, string $form): ?string
    {
        return $this->usesTurnstile() ? $this->verifyTurnstile($request) : $this->verifyBuiltIn($request, $form);
    }

    private function verifyBuiltIn(IncomingRequest $request, string $form): ?string
    {
        $key   = 'captcha_' . $form;
        $state = session()->get($key);
        session()->remove($key); // single use, even on failure

        if (! is_array($state)) {
            return 'The security check expired. Please try again.';
        }

        $age = time() - (int) $state['issued'];
        if ($age < self::MIN_SECONDS) {
            return 'That was very quick. Please check the details and send again.';
        }
        if ($age > self::MAX_SECONDS) {
            return 'The security check expired. Please try again.';
        }

        $given = trim((string) $request->getPost('ec_captcha'));
        if ($given === '' || ! ctype_digit($given) || (int) $given !== (int) $state['answer']) {
            return 'The security answer was wrong. Please try again.';
        }

        return null;
    }

    private function verifyTurnstile(IncomingRequest $request): ?string
    {
        $token = (string) $request->getPost('cf-turnstile-response');
        if ($token === '') {
            return 'Please complete the security check.';
        }

        try {
            $res  = service('curlrequest')->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'form_params' => ['secret' => $this->secretKey(), 'response' => $token, 'remoteip' => $request->getIPAddress()],
                'timeout'     => 5,
                'http_errors' => false,
            ]);
            $json = json_decode((string) $res->getBody(), true);
        } catch (\Throwable $e) {
            log_message('error', 'Turnstile verification failed: ' . $e->getMessage());

            return 'We could not verify the security check. Please try again.';
        }

        return ! empty($json['success']) ? null : 'The security check failed. Please try again.';
    }
}
