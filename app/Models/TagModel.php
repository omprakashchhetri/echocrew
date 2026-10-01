<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table         = 'tags';
    protected $allowedFields = ['name', 'slug'];
    protected $useTimestamps = true;
    protected $updatedField  = '';

    public function withCounts(): array
    {
        return $this->select('tags.*, (SELECT COUNT(*) FROM post_tags WHERE post_tags.tag_id = tags.id) AS post_count')
                    ->orderBy('tags.name')
                    ->findAll();
    }

    /**
     * Resolve a comma separated list of names into tag ids, creating missing tags.
     *
     * @return list<int>
     */
    public function idsFromNames(string $csv): array
    {
        $ids = [];

        foreach (array_filter(array_map('trim', explode(',', $csv))) as $name) {
            $name = mb_substr($name, 0, 100);
            $slug = url_title($name, '-', true);
            if ($slug === '') {
                continue;
            }

            $row = $this->where('slug', $slug)->first();
            if ($row) {
                $ids[] = (int) $row['id'];
            } else {
                $this->insert(['name' => $name, 'slug' => $slug]);
                $ids[] = (int) $this->getInsertID();
            }
        }

        return $ids;
    }
}
