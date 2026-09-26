<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlogModel;
use App\Models\CommentModel;

class Blog extends BaseController
{
    protected $blogModel;
    protected $commentModel;

    public function __construct()
    {
        $this->blogModel    = new BlogModel();
        $this->commentModel = new CommentModel();
    }

    public function index()
    {
        $data['posts'] = $this->blogModel
            ->withCategory()
            ->where('posts.status', 'published')
            ->orderBy('posts.created_at', 'DESC')
            ->paginate(10);

        $data['pager']       = $this->blogModel->pager;
        $data['title']       = 'Blog | EchoCrew';
        $data['description'] = 'Writing from the EchoCrew team on custom software, CRM, automation, integrations and running digital systems for growing businesses.';
        $data['canonical']   = base_url('blog');

        return view('blog/index', $data);
    }

    public function view($slug)
    {
        $post = $this->blogModel->getBySlug($slug);

        // Drafts are not public.
        if (! $post || ($post['status'] ?? '') !== 'published') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $ip = $this->request->getIPAddress();
        $cacheKey = 'viewed_post_' . $post['id'] . '_' . md5($ip); // hashed to avoid long keys

        // Only count view if IP hasn't viewed in last 10 minutes (600 seconds)
        if (!cache()->get($cacheKey)) {
            $this->blogModel->incrementViews($post['id']);
            cache()->save($cacheKey, true, 600);
        }

        $excerpt = trim(preg_replace('/\s+/', ' ', strip_tags((string) $post['content'])));

        $data['post']        = $post;
        $data['comments']    = $this->commentModel->getComments($post['id']);
        $data['title']       = $post['title'] . ' | EchoCrew';
        $data['description'] = mb_strimwidth($excerpt, 0, 158, '...');
        $data['canonical']   = base_url('blog/view/' . $post['slug']);
        $data['ogType']      = 'article';
        $data['breadcrumbs'] = [
            'Home'         => base_url(),
            'Blog'         => base_url('blog'),
            $post['title'] => base_url('blog/view/' . $post['slug']),
        ];

        return view('blog/view', $data);
    }

    public function comment($id)
    {
        if (! auth()->loggedIn()) {
            return redirect()->back()->with('error', 'Please log in to comment');
        }

        if (! $this->blogModel->where('status', 'published')->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $comment = trim((string) $this->request->getPost('comment'));
        if ($comment === '' || mb_strlen($comment) > 2000) {
            return redirect()->back()->with('error', 'Comments need to be between 1 and 2000 characters.');
        }

        $this->commentModel->save([
            'post_id' => $id,
            'user_id' => user_id(),
            'comment' => $comment,
        ]);

        return redirect()->back()->with('message', 'Comment added');
    }
}
