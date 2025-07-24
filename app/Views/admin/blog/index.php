<h1>All Blog Posts</h1>
<a href="<?= site_url('admin/blog/create') ?>">Create New Post</a>

<table>
    <tr>
        <th>Title</th><th>Category</th><th>Status</th><th>Views</th><th>Actions</th>
    </tr>
    <?php foreach ($posts as $post): ?>
        <tr>
            <td><?= esc($post['title']) ?></td>
            <td><?= esc($post['category_name']) ?></td>
            <td><?= esc($post['status']) ?></td>
            <td><?= esc($post['view_count']) ?></td>
            <td>
                <a href="<?= site_url('admin/blog/edit/' . $post['id']) ?>">Edit</a> |
                <a href="<?= site_url('admin/blog/delete/' . $post['id']) ?>" onclick="return confirm('Delete?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
