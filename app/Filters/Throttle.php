<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Per-IP rate limiting for POST requests (token bucket via CI's Throttler).
 *
 * Route usage: ['filter' => 'throttle:<bucket>,<capacity>,<seconds>']
 * e.g. 'throttle:enquiry,5,3600' allows 5 submissions per hour per IP.
 * Behind a proxy or CDN, set Config\App::$proxyIPs so getIPAddress() is the client.
 */
class Throttle implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (strtolower($request->getMethod()) !== 'post') {
            return;
        }

        $bucket   = $arguments[0] ?? 'default';
        $capacity = (int) ($arguments[1] ?? 10);
        $seconds  = (int) ($arguments[2] ?? 60);

        $throttler = service('throttler');
        $key       = 'throttle_' . md5($bucket . '|' . $request->getIPAddress());

        if ($throttler->check($key, $capacity, $seconds) === false) {
            $wait = max(1, (int) $throttler->getTokenTime());

            return service('response')
                ->setStatusCode(429)
                ->setHeader('Retry-After', (string) $wait)
                ->setBody(view('errors/html/error_429', ['wait' => $wait]));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
