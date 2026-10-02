<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CommentModel;

class CommentController extends BaseController
{
    public function index()
    {
        $model  = new CommentModel();
        $status = (string) $this->request->getGet('status');

        return view('admin/comments', [
            'pageTitle' => 'Comments',
            'comments'  => $model->forModeration($status)->paginate(25),
            'pager'     => $model->pager,
            'status'    => $status,
        ]);
    }

    public function status($id)
    {
        $status = (string) $this->request->getPost('status');
        if (! in_array($status, ['approved', 'hidden'], true)) {
            return redirect()->back()->with('error', 'Unknown status.');
        }

        (new CommentModel())->update($id, ['status' => $status]);

        return redirect()->back()->with('message', 'Comment ' . $status . '.');
    }

    public function delete($id)
    {
        (new CommentModel())->delete($id);

        return redirect()->back()->with('message', 'Comment deleted.');
    }
}
