<?php

if (! function_exists('blog_post_date')) {
    /** Publish date if set, otherwise creation date, as a timestamp (or null). */
    function blog_post_date(array $post): ?int
    {
        $when = $post['published_at'] ?: ($post['created_at'] ?? null);

        return $when ? (strtotime((string) $when) ?: null) : null;
    }
}

if (! function_exists('blog_reading_time')) {
    function blog_reading_time(string $html): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($html)) / 200));
    }
}

if (! function_exists('blog_cover_url')) {
    function blog_cover_url(array $post): ?string
    {
        return ! empty($post['cover_image']) ? base_url($post['cover_image']) : null;
    }
}

if (! function_exists('blog_placeholder_class')) {
    /** Stable colour variant (0-4) for posts without a cover image, based on the category. */
    function blog_placeholder_class(array $post): string
    {
        return 'eb-ph-' . (crc32((string) ($post['category_slug'] ?? $post['title'] ?? '')) % 5);
    }
}

if (! function_exists('blog_initials')) {
    function blog_initials(?string $name): string
    {
        $name = trim((string) $name);

        return $name === '' ? '?' : mb_strtoupper(mb_substr($name, 0, 1));
    }
}

if (! function_exists('blog_cover')) {
    /** Renders the cover <img>, or a designed placeholder when there is none. */
    function blog_cover(array $post, bool $eager = false): string
    {
        $url = blog_cover_url($post);
        if ($url) {
            $alt = $post['cover_alt'] ?: $post['title'];

            return '<img src="' . esc($url) . '" alt="' . esc((string) $alt, 'attr') . '" loading="' . ($eager ? 'eager' : 'lazy') . '" decoding="async">';
        }

        return '<span class="eb-ph ' . blog_placeholder_class($post) . '" aria-hidden="true"><b>' . esc(blog_initials($post['category_name'] ?? $post['title'])) . '</b></span>';
    }
}
