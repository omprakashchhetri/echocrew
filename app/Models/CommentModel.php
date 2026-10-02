<?php
namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $allowedFields = ['post_id', 'user_id', 'comment', 'status', 'created_at'];
    protected $useTimestamps = true;
    protected $updatedField  = '';

    /** Approved comments for the public post page. */
    public function getComments($post_id)
    {
        return $this->where('comments.post_id', $post_id)
                    ->where('comments.status', 'approved')
                    ->join('users', 'users.id = comments.user_id')
                    ->select('comments.*, users.username')
                    ->orderBy('comments.id', 'ASC')
                    ->findAll();
    }

    /** Moderation list across all posts. */
    public function forModeration(string $status = '')
    {
        $this->select('comments.*, users.username, posts.title AS post_title, posts.slug AS post_slug')
             ->join('users', 'users.id = comments.user_id', 'left')
             ->join('posts', 'posts.id = comments.post_id', 'left');

        if (in_array($status, ['approved', 'hidden'], true)) {
            $this->where('comments.status', $status);
        }

        return $this->orderBy('comments.id', 'DESC');
    }
}
