<?php

namespace App\Models;

use CodeIgniter\Model;

class BlogModel extends Model
{
    protected $table         = 'posts';
    protected $allowedFields = [
        'title', 'slug', 'excerpt', 'content', 'cover_image', 'cover_alt', 'status', 'published_at',
        'user_id', 'category_id', 'view_count',
    ];
    protected $useTimestamps = true;

    // The model already selects FROM posts; adding from('posts p') here would
    // cross-join the table with itself and repeat every row.
    public function withCategory()
    {
        return $this->select('posts.*, c.name as category_name, c.slug as category_slug, u.username as author_name')
                    ->join('categories c', 'c.id = posts.category_id', 'left')
                    ->join('users u', 'u.id = posts.user_id', 'left');
    }

    /** Published posts, newest first. Optional category slug / tag slug filters. */
    public function published(?string $categorySlug = null, ?string $tagSlug = null)
    {
        $this->withCategory()->where('posts.status', 'published');

        if ($categorySlug !== null) {
            $this->where('c.slug', $categorySlug);
        }
        if ($tagSlug !== null) {
            $this->join('post_tags pt', 'pt.post_id = posts.id')
                 ->join('tags t', 't.id = pt.tag_id')
                 ->where('t.slug', $tagSlug);
        }

        return $this->orderBy('COALESCE(posts.published_at, posts.created_at)', 'DESC', false);
    }

    public function getBySlug($slug)
    {
        return $this->withCategory()
                    ->where('posts.slug', $slug)
                    ->first();
    }

    /** Admin listing with optional q (title), status and category filters. */
    public function search(string $q = '', string $status = '', int $categoryId = 0)
    {
        $this->withCategory();

        if ($q !== '') {
            $this->like('posts.title', $q);
        }
        if (in_array($status, ['draft', 'published', 'archived'], true)) {
            $this->where('posts.status', $status);
        }
        if ($categoryId > 0) {
            $this->where('posts.category_id', $categoryId);
        }

        return $this->orderBy('posts.created_at', 'DESC');
    }

    /** Other published posts, same category first, newest first. */
    public function related(array $post, int $limit = 3): array
    {
        $same = $this->published()
            ->where('posts.id !=', $post['id'])
            ->where('posts.category_id', $post['category_id'])
            ->findAll($limit);

        if (count($same) >= $limit) {
            return $same;
        }

        $ids   = array_merge([(int) $post['id']], array_map('intval', array_column($same, 'id')));
        $extra = $this->published()->whereNotIn('posts.id', $ids)->findAll($limit - count($same));

        return array_merge($same, $extra);
    }

    public function incrementViews($id)
    {
        return $this->set('view_count', 'view_count+1', false)->where('id', $id)->update();
    }

    /** Builds a slug from $text that is not used by another post. */
    public function uniqueSlug(string $text, ?int $ignoreId = null): string
    {
        $base = url_title($text, '-', true) ?: 'post';
        $slug = $base;
        $i    = 2;

        while ($this->slugTaken($slug, $ignoreId)) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function slugTaken(string $slug, ?int $ignoreId = null): bool
    {
        $builder = $this->builder()->where('slug', $slug);
        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }

    /** @return list<array{id:int|string,name:string,slug:string}> */
    public function tagsFor(int $postId): array
    {
        return $this->db->table('tags')
            ->select('tags.id, tags.name, tags.slug')
            ->join('post_tags', 'post_tags.tag_id = tags.id')
            ->where('post_tags.post_id', $postId)
            ->orderBy('tags.name')
            ->get()->getResultArray();
    }

    /** Replace the post's tags with exactly $tagIds. */
    public function syncTags(int $postId, array $tagIds): void
    {
        $this->db->table('post_tags')->where('post_id', $postId)->delete();

        foreach (array_unique(array_map('intval', $tagIds)) as $tagId) {
            if ($tagId > 0) {
                $this->db->table('post_tags')->insert(['post_id' => $postId, 'tag_id' => $tagId]);
            }
        }
    }

    /** Remove a post together with its tag links and comments. */
    public function deleteWithRelations(int $id): void
    {
        $post = $this->find($id);
        (new \App\Libraries\ImageUploader())->remove($post['cover_image'] ?? null);

        $this->db->table('post_tags')->where('post_id', $id)->delete();
        $this->db->table('comments')->where('post_id', $id)->delete();
        $this->delete($id);
    }
}
