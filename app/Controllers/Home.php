<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $seo = config('Seo');

        return view('pages/home', [
            'title'       => 'EchoCrew | Custom Software, CRM & Digital Solutions in Siliguri',
            'description' => 'EchoCrew builds custom websites, CRM systems, management software, e-commerce platforms, API integrations, automation and AI-powered digital solutions around real business workflows. Based in Siliguri, West Bengal.',
            'canonical'   => base_url(),
            'schemaFaq'   => $seo->homeFaq,
        ]);
    }
}
