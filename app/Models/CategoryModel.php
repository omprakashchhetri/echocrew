<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $allowedFields = ['name', 'slug'];
    protected $useTimestamps = true;
    protected $updatedField  = '';

    /** Categories with their post counts. */
    public function withCounts(): array
    {
        return $this->select('categories.*, (SELECT COUNT(*) FROM posts WHERE posts.category_id = categories.id) AS post_count')
                    ->orderBy('categories.name')
                    ->findAll();
    }

    public function slugTaken(string $slug, ?int $ignoreId = null): bool
    {
        $b = $this->builder()->where('slug', $slug);
        if ($ignoreId !== null) {
            $b->where('id !=', $ignoreId);
        }

        return $b->countAllResults() > 0;
    }
}
