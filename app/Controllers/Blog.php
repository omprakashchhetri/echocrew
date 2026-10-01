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
        return $this->listing(null, null, 'Blog | EchoCrew', base_url('blog'));
    }

    public function category($slug)
    {
        $cat = (new \App\Models\CategoryModel())->where('slug', $slug)->first()
            ?? throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return $this->listing($slug, null, $cat['name'] . ' | Blog | EchoCrew', base_url('blog/category/' . $slug), $cat['name']);
    }

    public function tag($slug)
    {
        $tag = (new \App\Models\TagModel())->where('slug', $slug)->first()
            ?? throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return $this->listing(null, $slug, '#' . $tag['name'] . ' | Blog | EchoCrew', base_url('blog/tag/' . $slug), '#' . $tag['name']);
    }

    public function feed()
    {
        $posts = $this->blogModel->published()->findAll(20);

        $items = '';
        foreach ($posts as $p) {
            $url    = base_url('blog/view/' . $p['slug']);
            $when   = date(DATE_RSS, strtotime((string) ($p['published_at'] ?: $p['created_at'])));
            $items .= '<item><title>' . htmlspecialchars($p['title'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</title><link>' . $url . '</link><guid>' . $url . '</guid>'
                . '<pubDate>' . $when . '</pubDate><description>' . htmlspecialchars($this->excerptOf($p), ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</description></item>';
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel><title>EchoCrew Blog</title>'
            . '<link>' . base_url('blog') . '</link><description>Writing from the EchoCrew team.</description>'
            . $items . '</channel></rss>';

        return $this->response->setContentType('application/rss+xml')->setBody($xml);
    }

    private function listing(?string $categorySlug, ?string $tagSlug, string $title, string $canonical, ?string $heading = null)
    {
        $data['posts'] = $this->blogModel->published($categorySlug, $tagSlug)->paginate(10);

        foreach ($data['posts'] as &$post) {
            $post['summary'] = $this->excerptOf($post);
        }
        unset($post);

        $data['pager']       = $this->blogModel->pager;
        $data['heading']     = $heading;
        $data['categories']  = (new \App\Models\CategoryModel())->orderBy('name')->findAll();
        $data['title']       = $title;
        $data['description'] = 'Writing from the EchoCrew team on custom software, CRM, automation, integrations and running digital systems for growing businesses.';
        $data['canonical']   = $canonical;

        return view('blog/index', $data);
    }

    /** Manual excerpt if set, otherwise the first ~160 characters of the body. */
    private function excerptOf(array $post): string
    {
        if (! empty($post['excerpt'])) {
            return $post['excerpt'];
        }

        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $post['content'])));

        return mb_strimwidth($text, 0, 158, '...');
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

        $data['post']        = $post;
        $data['tags']        = $this->blogModel->tagsFor((int) $post['id']);
        $data['comments']    = $this->commentModel->getComments($post['id']);
        $data['title']       = $post['title'] . ' | EchoCrew';
        $data['description'] = $this->excerptOf($post);
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
