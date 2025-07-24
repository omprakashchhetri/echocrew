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
            ->orderBy('created_at', 'DESC')
            ->paginate(5);

        $data['pager'] = $this->blogModel->pager;

        return view('blog/index', $data);
    }

    public function view($slug)
    {
        $post = $this->blogModel->getBySlug($slug);

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $ip = $this->request->getIPAddress();
        $cacheKey = 'viewed_post_' . $post['id'] . '_' . md5($ip); // hashed to avoid long keys

        // Only count view if IP hasn't viewed in last 10 minutes (600 seconds)
        if (!cache()->get($cacheKey)) {
            $this->blogModel->incrementViews($post['id']);
            cache()->save($cacheKey, true, 600);
        }

        $data['post'] = $post;
        $data['comments'] = $this->commentModel->getComments($post['id']);

        return view('blog/view', $data);
    }

    public function comment($id)
{
    // ❗ Block guests from posting
    if (!auth()->loggedIn()) {
        return redirect()->back()->with('error', 'Please log in to comment');
    }

    $data = [
        'post_id' => $id,
        'user_id' => user_id(), // ✅ Shield helper to get user ID
        'comment' => $this->request->getPost('comment'),
    ];

    $this->commentModel->save($data);
    return redirect()->back()->with('message', 'Comment added');
}

}
