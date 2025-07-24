<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BlogModel;
use App\Models\CategoryModel;
use App\Models\TagModel;

class BlogController extends BaseController
{
    protected $blogModel;
    protected $categoryModel;
    protected $tagModel;

    public function __construct()
    {
        $this->blogModel     = new BlogModel();
        $this->categoryModel = new CategoryModel();
        // $this->tagModel      = new TagModel();
    }

    public function index()
    {
        $data['posts'] = $this->blogModel
            ->withCategory()
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('admin/blog/index', $data);
    }

    public function create()
    {
        $data['categories'] = $this->categoryModel->findAll();
        return view('admin/blog/create', $data);
    }

    public function store()
    {
        $data = $this->request->getPost();

        $data['slug']     = url_title($data['title'], '-', true);
        $data['user_id']  = user_id();
        $data['view_count'] = 0;

        $this->blogModel->save($data);

        return redirect()->to('/admin/blog')->with('message', 'Post created');
    }

    public function edit($id)
    {
        $data['post']      = $this->blogModel->find($id);
        $data['categories'] = $this->categoryModel->findAll();
        return view('admin/blog/edit', $data);
    }

    public function update($id)
    {
        $data = $this->request->getPost();

        $data['id']     = $id;
        $data['slug']   = url_title($data['title'], '-', true);

        $this->blogModel->save($data);

        return redirect()->to('/admin/blog')->with('message', 'Post updated');
    }

    public function delete($id)
    {
        $this->blogModel->delete($id);
        return redirect()->to('/admin/blog')->with('message', 'Post deleted');
    }
}
