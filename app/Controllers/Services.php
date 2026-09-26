<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class Services extends BaseController
{
    public function index()
    {
        $seo = config('Seo');

        return view('pages/services', [
            'title'       => 'Software Development Services in Siliguri | ' . $seo->brand,
            'description' => 'EchoCrew services: custom software, website development, CRM, school management software, e-commerce, API integration, automation, AI and SEO, built for businesses in Siliguri and beyond.',
            'canonical'   => base_url('services'),
            'services'    => $seo->services,
            'breadcrumbs' => [
                'Home'     => base_url(),
                'Services' => base_url('services'),
            ],
        ]);
    }

    public function show(string $slug)
    {
        $seo = config('Seo');

        if (! isset($seo->services[$slug])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $service = $seo->services[$slug];

        return view('pages/service', [
            'title'         => $service['title'],
            'description'   => $service['desc'],
            'canonical'     => base_url('services/' . $slug),
            'slug'          => $slug,
            'service'       => $service,
            'schemaService' => $service,
            'schemaFaq'     => $service['faq'] ?? [],
            'related'       => array_slice(array_diff_key($seo->services, [$slug => true]), 0, 6, true),
            'breadcrumbs'   => [
                'Home'           => base_url(),
                'Services'       => base_url('services'),
                $service['name'] => base_url('services/' . $slug),
            ],
        ]);
    }
}
