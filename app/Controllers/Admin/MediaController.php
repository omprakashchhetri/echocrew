<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ImageUploader;

/** Inline image uploads for the post editor. Returns JSON. */
class MediaController extends BaseController
{
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
