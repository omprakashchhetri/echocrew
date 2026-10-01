<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TagModel;

class TagController extends BaseController
{
    public function index()
    {
        return view('admin/tags', [
            'pageTitle' => 'Tags',
            'tags'      => (new TagModel())->withCounts(),
        ]);
    }

    public function store()
    {
        if (! $this->validate(['name' => 'required|min_length[2]|max_length[100]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        (new TagModel())->idsFromNames((string) $this->request->getPost('name'));

        return redirect()->to('/admin/tags')->with('message', 'Tag saved.');
    }

    public function delete($id)
    {
        db_connect()->table('post_tags')->where('tag_id', $id)->delete();
        (new TagModel())->delete($id);

        return redirect()->to('/admin/tags')->with('message', 'Tag deleted.');
    }
}
