<h1>Create Blog Post</h1>
<form method="post" action="<?= site_url('admin/blog/store') ?>">
    <?= csrf_field() ?>
    <input type="text" name="title" placeholder="Title" required><br>
    
    <textarea name="content" id="editor"></textarea><br>

    <select name="category_id">
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <select name="status">
        <option value="draft">Draft</option>
        <option value="published">Published</option>
        <option value="archived">Archived</option>
    </select><br>

    <button type="submit">Save</button>
</form>

<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
<script>CKEDITOR.replace('editor');</script>
