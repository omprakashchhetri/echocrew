<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ImageUploader;

/** Media library (browse, upload, delete) and the JSON upload used by the post editor. */
class MediaController extends BaseController
{
    private const PER_PAGE = 24;

    public function index()
    {
        $all   = glob(FCPATH . 'uploads/blog/*/*/*.webp') ?: [];
        usort($all, static fn ($a, $b) => filemtime($b) <=> filemtime($a));

        $total = count($all);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page  = min($pages, max(1, (int) $this->request->getGet('page')));
        $slice = array_slice($all, ($page - 1) * self::PER_PAGE, self::PER_PAGE);

        $posts = (new \App\Models\BlogModel())->select('id, title, cover_image, content')->findAll();

        $items = [];
        foreach ($slice as $abs) {
            $rel  = substr($abs, strlen(FCPATH));
            $dims = @getimagesize($abs) ?: [0, 0];
            $used = [];
            foreach ($posts as $p) {
                if ($p['cover_image'] === $rel || str_contains((string) $p['content'], $rel)) {
                    $used[] = ['id' => $p['id'], 'title' => $p['title']];
                }
            }
            $items[] = [
                'path'  => $rel,
                'url'   => base_url($rel),
                'size'  => filesize($abs),
                'w'     => $dims[0],
                'h'     => $dims[1],
                'time'  => filemtime($abs),
                'used'  => $used,
            ];
        }

        return view('admin/media', [
            'pageTitle' => 'Media',
            'items'     => $items,
            'total'     => $total,
            'page'      => $page,
            'pages'     => $pages,
        ]);
    }

    /** Library upload form: one or more images. */
    public function store()
    {
        $files = $this->request->getFileMultiple('images') ?: [];
        $ok    = 0;
        $errs  = [];
        $up    = new ImageUploader();

        foreach ($files as $file) {
            if ($file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            try {
                $up->store($file);
                $ok++;
            } catch (\RuntimeException $e) {
                $errs[] = $file->getClientName() . ': ' . $e->getMessage();
            }
        }

        $redirect = redirect()->to('/admin/media');
        if ($errs) {
            $redirect->with('errors', $errs);
        }
        if ($ok) {
            $redirect->with('message', $ok . ' image' . ($ok > 1 ? 's' : '') . ' uploaded.');
        } elseif (! $errs) {
            $redirect->with('error', 'Choose at least one image.');
        }

        return $redirect;
    }

    public function delete()
    {
        $path = (string) $this->request->getPost('path');

        if (! preg_match('#^uploads/blog/\d{4}/\d{2}/[a-f0-9]+\.webp$#', $path) || ! is_file(FCPATH . $path)) {
            return redirect()->to('/admin/media')->with('error', 'File not found.');
        }

        $blog = new \App\Models\BlogModel();
        $used = $blog->groupStart()->where('cover_image', $path)->orLike('content', $path)->groupEnd()->countAllResults();
        if ($used > 0) {
            return redirect()->to('/admin/media')->with('error', 'That image is used by ' . $used . ' post(s). Remove it from them first.');
        }

        (new ImageUploader())->remove($path);

        return redirect()->to('/admin/media')->with('message', 'Image deleted.');
    }

    /** Inline image upload for the post editor. Returns JSON. */
    public function upload()
    {
        $file = $this->request->getFile('image');

        try {
            if (! $file) {
                throw new \RuntimeException('No file received.');
            }
            $saved = (new ImageUploader())->store($file);
        } catch (\RuntimeException $e) {
            return $this->response->setStatusCode(422)->setJSON(['error' => $e->getMessage(), 'csrf' => csrf_hash()]);
        }

        return $this->response->setJSON(['url' => $saved['url'], 'csrf' => csrf_hash()]);
    }
}
