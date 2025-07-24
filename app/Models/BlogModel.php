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

    public function withCategory()
    {
        return $this->select('p.*, c.name as category_name')
                    ->from('posts p')
                    ->join('categories c', 'c.id = p.category_id', 'left');
    }

    public function getBySlug($slug)
    {
        return $this->withCategory()
                    ->where('p.slug', $slug)
                    ->first();
    }



    public function incrementViews($id)
    {
        return $this->set('view_count', 'view_count+1', false)->where('id', $id)->update();
    }
}
