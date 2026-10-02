<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\HtmlSanitizer;
use App\Models\BlogModel;
use App\Models\CategoryModel;
use App\Models\TagModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class BlogController extends BaseController
{
    protected BlogModel $blogModel;
    protected CategoryModel $categoryModel;
    protected TagModel $tagModel;

    public function __construct()
    {
        $this->blogModel     = new BlogModel();
        $this->categoryModel = new CategoryModel();
        $this->tagModel      = new TagModel();
    }

    public function index()
    {
        $q      = trim((string) $this->request->getGet('q'));
        $status = (string) $this->request->getGet('status');
        $cat    = (int) $this->request->getGet('category');

        return view('admin/blog/index', [
            'pageTitle'  => 'Posts',
            'posts'      => $this->blogModel->search($q, $status, $cat)->paginate(20),
            'pager'      => $this->blogModel->pager,
            'categories' => $this->categoryModel->orderBy('name')->findAll(),
            'filters'    => ['q' => $q, 'status' => $status, 'category' => $cat],
        ]);
    }

    public function create()
    {
        return view('admin/blog/form', [
            'pageTitle'  => 'New post',
            'post'       => null,
            'categories' => $this->categoryModel->orderBy('name')->findAll(),
            'allTags'    => $this->tagModel->orderBy('name')->findAll(),
            'postTagIds' => [],
        ]);
    }

    public function store()
    {
        $data = $this->collect(null);
        if ($data === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data['user_id']    = user_id();
        $data['view_count'] = 0;

        $id = $this->blogModel->insert($data, true);
        $this->blogModel->syncTags((int) $id, $this->requestedTagIds());

        return redirect()->to('/admin/blog')->with('message', 'Post created.');
    }

    public function edit($id)
    {
        $post = $this->blogModel->find($id) ?? throw PageNotFoundException::forPageNotFound();

        return view('admin/blog/form', [
            'pageTitle'  => 'Edit post',
            'post'       => $post,
            'categories' => $this->categoryModel->orderBy('name')->findAll(),
            'allTags'    => $this->tagModel->orderBy('name')->findAll(),
            'postTagIds' => array_map('intval', array_column($this->blogModel->tagsFor((int) $id), 'id')),
        ]);
    }

    public function update($id)
    {
        $post = $this->blogModel->find($id) ?? throw PageNotFoundException::forPageNotFound();

        $data = $this->collect($post);
        if ($data === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->blogModel->update($id, $data);
        $this->blogModel->syncTags((int) $id, $this->requestedTagIds());

        return redirect()->to('/admin/blog')->with('message', 'Post updated.');
    }

    public function delete($id)
    {
        $this->blogModel->find($id) ?? throw PageNotFoundException::forPageNotFound();
        $this->blogModel->deleteWithRelations((int) $id);

        return redirect()->to('/admin/blog')->with('message', 'Post deleted.');
    }

    /** Quick status change from the list (publish / unpublish / archive). */
    public function status($id)
    {
        $post   = $this->blogModel->find($id) ?? throw PageNotFoundException::forPageNotFound();
        $status = (string) $this->request->getPost('status');

        if (! in_array($status, ['draft', 'published', 'archived'], true)) {
            return redirect()->back()->with('error', 'Unknown status.');
        }

        $this->blogModel->update($id, [
            'status'       => $status,
            'published_at' => $status === 'published' ? ($post['published_at'] ?: date('Y-m-d H:i:s')) : $post['published_at'],
        ]);

        return redirect()->back()->with('message', 'Status set to ' . $status . '.');
    }

    /**
     * Validates the submitted post and returns only whitelisted, sanitised fields,
     * or null when validation fails.
     *
     * @param array<string,mixed>|null $existing
     */
    private function collect(?array $existing): ?array
    {
        $id = $existing['id'] ?? null;

        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'content'     => 'required',
            'excerpt'     => 'permit_empty|max_length[300]',
            'status'      => 'required|in_list[draft,published,archived]',
            'category_id' => 'required|is_natural_no_zero|is_not_unique[categories.id]',
            'slug'        => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return null;
        }

        $title = trim((string) $this->request->getPost('title'));
        $slug  = url_title((string) $this->request->getPost('slug'), '-', true);

        if ($slug === '') {
            $slug = $existing['slug'] ?? $this->blogModel->uniqueSlug($title);
        } elseif ($this->blogModel->slugTaken($slug, $id !== null ? (int) $id : null)) {
            $this->validator->setError('slug', 'That slug is already used by another post.');

            return null;
        }

        $status      = (string) $this->request->getPost('status');
        $publishedAt = $existing['published_at'] ?? null;
        if ($status === 'published' && ! $publishedAt) {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $content = (new HtmlSanitizer())->clean((string) $this->request->getPost('content'));
        if ($content === '') {
            $this->validator->setError('content', 'The post body is empty after removing unsupported markup.');

            return null;
        }

        $excerpt = trim((string) $this->request->getPost('excerpt'));

        $cover      = $existing['cover_image'] ?? null;
        $uploader   = new \App\Libraries\ImageUploader();
        $coverFile  = $this->request->getFile('cover');

        if ($coverFile && $coverFile->getError() !== UPLOAD_ERR_NO_FILE) {
            try {
                $saved = $uploader->store($coverFile);
            } catch (\RuntimeException $e) {
                $this->validator->setError('cover', $e->getMessage());

                return null;
            }
            $uploader->remove($cover);
            $cover = $saved['path'];
        } elseif ($this->request->getPost('remove_cover')) {
            $uploader->remove($cover);
            $cover = null;
        }

        $coverAlt = trim((string) $this->request->getPost('cover_alt'));

        return [
            'cover_image'  => $cover,
            'cover_alt'    => $coverAlt !== '' ? mb_substr($coverAlt, 0, 200) : null,
            'title'        => $title,
            'slug'         => $slug,
            'content'      => $content,
            'excerpt'      => $excerpt !== '' ? $excerpt : null,
            'status'       => $status,
            'category_id'  => (int) $this->request->getPost('category_id'),
            'published_at' => $publishedAt,
        ];
    }

    /** @return list<int> */
    private function requestedTagIds(): array
    {
        $ids = array_map('intval', (array) $this->request->getPost('tags'));

        return array_merge($ids, $this->tagModel->idsFromNames((string) $this->request->getPost('new_tags')));
    }
}
