<h1>Edit Blog Post</h1>
<form method="post" action="<?= site_url('admin/blog/update/' . $post['id']) ?>">
    <?= csrf_field() ?>
    <input type="text" name="title" value="<?= esc($post['title']) ?>" required><br>
    
    <textarea name="content" id="editor"><?= $post['content'] ?></textarea><br>

    <select name="category_id">
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $post['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                <?= esc($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <select name="status">
        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
        <option value="archived" <?= $post['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
    </select><br>

    <button type="submit">Update</button>
</form>

<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
<script>CKEDITOR.replace('editor');</script>
