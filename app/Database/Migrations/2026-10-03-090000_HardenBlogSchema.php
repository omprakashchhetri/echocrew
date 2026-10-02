<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Schema hardening for the blog tables:
 *  - unique category/tag slugs and unique post/tag links (existing duplicates are repaired first)
 *  - indexes for the queries the public blog and admin run
 *  - posts.content becomes MEDIUMTEXT on MySQL (TEXT truncates at 64 KB)
 */
class HardenBlogSchema extends Migration
{
    public function up()
    {
        $this->dedupeSlugs('categories');
        $this->dedupeSlugs('tags');
        $this->dedupePostTags();

        if ($this->db->DBDriver === 'MySQLi') {
            $this->db->query('ALTER TABLE ' . $this->db->prefixTable('posts') . ' MODIFY content MEDIUMTEXT NOT NULL');
        }

        $indexes = [
            ['UNIQUE INDEX', 'ux_categories_slug',   'categories', 'slug'],
            ['UNIQUE INDEX', 'ux_tags_slug',         'tags',       'slug'],
            ['UNIQUE INDEX', 'ux_post_tags',         'post_tags',  'post_id, tag_id'],
            ['INDEX',        'ix_post_tags_tag',     'post_tags',  'tag_id'],
            ['INDEX',        'ix_posts_list',        'posts',      'status, published_at'],
            ['INDEX',        'ix_posts_category',    'posts',      'category_id'],
            ['INDEX',        'ix_posts_user',        'posts',      'user_id'],
            ['INDEX',        'ix_comments_post',     'comments',   'post_id, status'],
            ['INDEX',        'ix_enquiries_status',  'enquiries',  'status, created_at'],
        ];

        foreach ($indexes as [$kind, $name, $table, $cols]) {
            $this->db->query("CREATE {$kind} {$name} ON " . $this->db->prefixTable($table) . " ({$cols})");
        }
    }

    public function down()
    {
        $drop = [
            'ux_categories_slug' => 'categories', 'ux_tags_slug' => 'tags', 'ux_post_tags' => 'post_tags',
            'ix_post_tags_tag' => 'post_tags', 'ix_posts_list' => 'posts', 'ix_posts_category' => 'posts',
            'ix_posts_user' => 'posts', 'ix_comments_post' => 'comments', 'ix_enquiries_status' => 'enquiries',
        ];

        foreach ($drop as $name => $table) {
            $sql = $this->db->DBDriver === 'MySQLi'
                ? "DROP INDEX {$name} ON " . $this->db->prefixTable($table)
                : "DROP INDEX {$name}";
            $this->db->query($sql);
        }
    }

    /** Rename repeated slugs so a unique index can be added. */
    private function dedupeSlugs(string $table): void
    {
        $seen = [];
        foreach ($this->db->table($table)->select('id, slug')->orderBy('id')->get()->getResultArray() as $row) {
            if (isset($seen[$row['slug']])) {
                $this->db->table($table)->where('id', $row['id'])->update(['slug' => $row['slug'] . '-' . $row['id']]);
            }
            $seen[$row['slug']] = true;
        }
    }

    private function dedupePostTags(): void
    {
        $dups = $this->db->query('SELECT post_id, tag_id FROM ' . $this->db->prefixTable('post_tags') . ' GROUP BY post_id, tag_id HAVING COUNT(*) > 1')->getResultArray();

        foreach ($dups as $d) {
            $this->db->table('post_tags')->where($d)->delete();
            $this->db->table('post_tags')->insert($d);
        }
    }
}
