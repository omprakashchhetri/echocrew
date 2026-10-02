<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Safe image uploads for the blog.
 *
 * The file is decoded and re-encoded with GD, which drops EXIF/metadata and any
 * payload hidden in the original bytes. The stored name is random and the
 * extension is chosen by us, never taken from the client.
 */
class ImageUploader
{
    public const MAX_BYTES  = 5 * 1024 * 1024;
    public const MAX_PIXELS = 40_000_000;
    private const TYPES     = [IMAGETYPE_JPEG => 'jpeg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];

    /**
     * @return array{path:string,url:string}  path relative to public/, e.g. uploads/blog/2026/10/ab12.webp
     *
     * @throws \RuntimeException with a message safe to show to the admin
     */
    public function store(UploadedFile $file, int $maxWidth = 1600): array
    {
        $this->assertUploaded($file);

        if ($file->getSize() > self::limitBytes()) {
            throw new \RuntimeException('Image is larger than ' . self::limitLabel() . '.');
        }

        $info = @getimagesize($file->getTempName());
        if ($info === false || ! isset(self::TYPES[$info[2]])) {
            throw new \RuntimeException('Only JPG, PNG, WebP or GIF images are allowed.');
        }
        if ($info[0] * $info[1] > self::MAX_PIXELS) {
            throw new \RuntimeException('Image dimensions are too large.');
        }

        $create = 'imagecreatefrom' . self::TYPES[$info[2]];
        $img    = @$create($file->getTempName());
        if (! $img) {
            throw new \RuntimeException('That image could not be read.');
        }

        // Palette images (indexed PNG/GIF) cannot be written as WebP.
        if (! imageistruecolor($img)) {
            imagepalettetotruecolor($img);
        }

        if ($info[2] === IMAGETYPE_JPEG) {
            $img = $this->applyOrientation($img, $file->getTempName());
        }

        if (imagesx($img) > $maxWidth) {
            $scaled = imagescale($img, $maxWidth, -1, IMG_BICUBIC);
            if ($scaled) {
                $img = $scaled;
            }
        }

        imagealphablending($img, false);
        imagesavealpha($img, true);

        $dir = 'uploads/blog/' . date('Y/m');
        $abs = FCPATH . $dir;
        if (! is_dir($abs) && ! mkdir($abs, 0755, true) && ! is_dir($abs)) {
            throw new \RuntimeException('The upload folder is not writable.');
        }

        $name = bin2hex(random_bytes(10)) . '.webp';
        if (! imagewebp($img, $abs . '/' . $name, 82)) {
            throw new \RuntimeException('The image could not be saved.');
        }

        return ['path' => $dir . '/' . $name, 'url' => base_url($dir . '/' . $name)];
    }

    /** Largest accepted upload in bytes: our cap, further limited by php.ini. */
    public static function limitBytes(): int
    {
        $limits = array_filter([self::MAX_BYTES, self::iniBytes('upload_max_filesize'), self::iniBytes('post_max_size')]);

        return (int) min($limits);
    }

    public static function limitLabel(): string
    {
        $mb = self::limitBytes() / 1048576;

        return (floor($mb) == $mb ? (int) $mb : number_format($mb, 1)) . ' MB';
    }

    private static function iniBytes(string $key): int
    {
        $v = trim((string) ini_get($key));
        if ($v === '' || $v === '-1') {
            return 0;
        }
        $n = (float) $v;

        return (int) match (strtolower(substr($v, -1))) {
            'g'     => $n * 1073741824,
            'm'     => $n * 1048576,
            'k'     => $n * 1024,
            default => $n,
        };
    }

    private function assertUploaded(UploadedFile $file): void
    {
        $err = $file->getError();

        if ($err === UPLOAD_ERR_OK && $file->isValid() && ! $file->hasMoved()) {
            return;
        }

        throw new \RuntimeException(match ($err) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Image is larger than the server allows (' . self::limitLabel() . '). Use a smaller image.',
            UPLOAD_ERR_PARTIAL                        => 'The upload was interrupted. Try again.',
            UPLOAD_ERR_NO_FILE                        => 'No file was selected.',
            UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE => 'The server cannot store uploads right now (temp folder problem). Contact the developer.',
            default                                   => 'The upload failed. Try again.',
        });
    }

    /** Rotate JPEGs according to their EXIF orientation (phone photos). */
    private function applyOrientation(\GdImage $img, string $path): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $img;
        }

        $exif = @exif_read_data($path);
        $deg  = match ((int) ($exif['Orientation'] ?? 1)) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($deg !== 0 && ($rotated = imagerotate($img, $deg, 0))) {
            return $rotated;
        }

        return $img;
    }

    /** Delete a previously stored file. Only paths under uploads/blog/ are ever touched. */
    public function remove(?string $path): void
    {
        if ($path && preg_match('#^uploads/blog/\d{4}/\d{2}/[a-f0-9]+\.webp$#', $path)) {
            @unlink(FCPATH . $path);
        }
    }
}
