<?php
namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $allowedFields = ['post_id', 'user_id', 'comment', 'created_at'];
    // protected $useTimestamps = true;

    public function getComments($post_id)
    {
        return $this->where('post_id', $post_id)
                    ->join('users', 'users.id = comments.user_id')
                    ->select('comments.*, users.username')
                    ->findAll();
    }
}
