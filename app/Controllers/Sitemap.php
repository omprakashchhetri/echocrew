<?php

namespace App\Controllers;

use App\Models\BlogModel;

class Sitemap extends BaseController
{
    public function index()
    {
        $seo  = config('Seo');
        $urls = [
            ['loc' => base_url(),          'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => base_url('services'), 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['loc' => base_url('blog'),    'priority' => '0.7', 'changefreq' => 'weekly'],
        ];

        foreach (array_keys($seo->services) as $slug) {
            $urls[] = ['loc' => base_url('services/' . $slug), 'priority' => '0.8', 'changefreq' => 'monthly'];
        }

        // Blog posts, if the table exists.
        try {
            $posts = (new BlogModel())->select('slug, updated_at')->findAll();
            foreach ($posts as $post) {
                $urls[] = [
                    'loc'        => base_url('blog/view/' . $post['slug']),
                    'lastmod'    => isset($post['updated_at']) ? date('Y-m-d', strtotime((string) $post['updated_at'])) : null,
                    'priority'   => '0.6',
                    'changefreq' => 'monthly',
                ];
            }
        } catch (\Throwable $e) {
            log_message('notice', 'Sitemap: blog posts skipped — ' . $e->getMessage());
        }

        return $this->response
            ->setContentType('application/xml')
            ->setBody(view('sitemap/index', ['urls' => $urls]));
    }
}
