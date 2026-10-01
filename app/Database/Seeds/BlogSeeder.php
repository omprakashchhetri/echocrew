<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds blog categories and starter draft posts.
 * Run: php spark db:seed BlogSeeder
 * Requires at least one user (php spark shield:user create).
 */
class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $categories = [
            'Custom Software'   => 'custom-software',
            'CRM & Automation'  => 'crm-automation',
            'Education'         => 'education',
            'Engineering'       => 'engineering',
            'Web & SEO'         => 'web-seo',
        ];

        $categoryIds = [];
        foreach ($categories as $name => $slug) {
            $row = $this->db->table('categories')->where('slug', $slug)->get()->getRowArray();
            if (! $row) {
                $this->db->table('categories')->insert(['name' => $name, 'slug' => $slug, 'created_at' => $now]);
                $categoryIds[$slug] = $this->db->insertID();
            } else {
                $categoryIds[$slug] = $row['id'];
            }
        }

        $user = $this->db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
        if (! $user) {
            echo "No users found. Create one with `php spark shield:user create`, then re-run.\n";

            return;
        }

        $posts = [
            [
                'title'    => 'Custom software or off-the-shelf: how to decide',
                'category' => 'custom-software',
                'service'  => 'custom-software-development',
                'points'   => ['How much of your workflow is genuinely unique', 'Total cost over three years, not just licence price', 'Integration and data ownership', 'When to start with off-the-shelf and customise later'],
            ],
            [
                'title'    => 'What a CRM should do for a small business',
                'category' => 'crm-automation',
                'service'  => 'crm-development',
                'points'   => ['Lead capture and follow-up reminders', 'One view of every customer', 'Simple reporting owners will actually read', 'Why adoption matters more than features'],
            ],
            [
                'title'    => 'Signs your school needs management software',
                'category' => 'education',
                'service'  => 'school-management-software',
                'points'   => ['Fee tracking and receipts in spreadsheets', 'Attendance and result delays', 'Parent communication gaps', 'What to ask a vendor before you sign'],
            ],
            [
                'title'    => 'Five processes worth automating first',
                'category' => 'crm-automation',
                'service'  => 'business-automation',
                'points'   => ['Enquiry routing and acknowledgements', 'Invoicing and payment reminders', 'Report generation', 'Data sync between tools', 'How to measure the time saved'],
            ],
            [
                'title'    => 'Modernising a legacy system without stopping the business',
                'category' => 'engineering',
                'service'  => 'legacy-system-modernization',
                'points'   => ['Mapping what the old system really does', 'Incremental replacement over big-bang rewrites', 'Data migration and rollback plans', 'Keeping staff productive during the change'],
            ],
            [
                'title'    => 'Website basics for local search in Siliguri',
                'category' => 'web-seo',
                'service'  => 'digital-marketing-seo',
                'points'   => ['Google Business Profile', 'Location and service pages', 'Page speed on mobile networks', 'Reviews and local citations'],
            ],
        ];

        foreach ($posts as $post) {
            $slug = url_title($post['title'], '-', true);
            if ($this->db->table('posts')->where('slug', $slug)->countAllResults() > 0) {
                continue;
            }

            $items = implode('', array_map(static fn ($p) => '<li>' . esc($p) . '</li>', $post['points']));
            $body  = '<p>Draft intro: state the problem in two sentences.</p>'
                . '<h2>What to cover</h2><ul>' . $items . '</ul>'
                . '<p>Next step: <a href="/services/' . $post['service'] . '">see our related service</a> or '
                . '<a href="/#contact">send us an enquiry</a>.</p>';

            $this->db->table('posts')->insert([
                'title'       => $post['title'],
                'slug'        => $slug,
                'content'     => $body,
                'excerpt'     => null,
                'status'      => 'draft',
                'view_count'  => 0,
                'user_id'     => $user['id'],
                'category_id' => $categoryIds[$post['category']],
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }
}
