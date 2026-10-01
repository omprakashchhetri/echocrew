<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class CategoryController extends BaseController
{
    public function index()
    {
        $model = new CategoryModel();

        return view('admin/categories', [
            'pageTitle'  => 'Categories',
            'categories' => $model->withCounts(),
            'editing'    => ($id = (int) $this->request->getGet('edit')) > 0 ? $model->find($id) : null,
        ]);
    }

    public function save($id = null)
    {
        $model = new CategoryModel();

        if (! $this->validate(['name' => 'required|min_length[2]|max_length[100]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = trim((string) $this->request->getPost('name'));
        $slug = url_title($name, '-', true);

        if ($slug === '' || $model->slugTaken($slug, $id !== null ? (int) $id : null)) {
            return redirect()->back()->withInput()->with('error', 'A category with that name already exists.');
        }

        if ($id !== null) {
            $model->find($id) ?? throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            $model->update($id, ['name' => $name, 'slug' => $slug]);
        } else {
            $model->insert(['name' => $name, 'slug' => $slug]);
        }

        return redirect()->to('/admin/categories')->with('message', 'Category saved.');
    }

    public function delete($id)
    {
        $inUse = db_connect()->table('posts')->where('category_id', $id)->countAllResults();
        if ($inUse > 0) {
            return redirect()->back()->with('error', "Can't delete: {$inUse} post(s) use this category. Move them first.");
        }

        (new CategoryModel())->delete($id);

        return redirect()->to('/admin/categories')->with('message', 'Category deleted.');
    }
}
