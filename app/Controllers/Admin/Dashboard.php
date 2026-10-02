<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BlogModel;
use App\Models\CommentModel;
use App\Models\EnquiryModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $posts     = new BlogModel();
        $enquiries = new EnquiryModel();

        return view('admin/dashboard', [
            'pageTitle'      => 'Dashboard',
            'published'      => $posts->where('status', 'published')->countAllResults(),
            'drafts'         => $posts->where('status', 'draft')->countAllResults(),
            'newEnquiries'   => $enquiries->where('status', 'new')->countAllResults(),
            'comments'       => (new CommentModel())->countAllResults(),
            'recentEnquiries' => $enquiries->orderBy('id', 'DESC')->findAll(5),
            'recentPosts'    => $posts->withCategory()->orderBy('posts.updated_at', 'DESC')->findAll(5),
            'topPosts'       => $posts->withCategory()->where('posts.status', 'published')->orderBy('posts.view_count', 'DESC')->findAll(5),
        ]);
    }
}
