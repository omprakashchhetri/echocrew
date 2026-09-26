<?php

namespace App\Models;

use CodeIgniter\Model;

class BlogModel extends Model
{
    protected $table = 'posts';
    protected $allowedFields = [
        'title', 'slug', 'content', 'status', 'user_id', 'category_id', 'view_count'
    ];
    protected $useTimestamps = true;

    // The model already selects FROM posts; adding from('posts p') here would
    // cross-join the table with itself and repeat every row.
    public function withCategory()
    {
        return $this->select('posts.*, c.name as category_name')
                    ->join('categories c', 'c.id = posts.category_id', 'left');
    }

    public function getBySlug($slug)
    {
        return $this->withCategory()
                    ->where('posts.slug', $slug)
                    ->first();
    }



    public function incrementViews($id)
    {
        return $this->set('view_count', 'view_count+1', false)->where('id', $id)->update();
    }
}
