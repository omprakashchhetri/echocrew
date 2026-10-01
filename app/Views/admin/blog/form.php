<?php
$editing = $post !== null;
$val     = static fn (string $k, $default = '') => old($k, $post[$k] ?? $default);
$checked = array_map('intval', (array) old('tags', $postTagIds));
?>
<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<form method="post" action="<?= site_url($editing ? 'admin/blog/update/' . $post['id'] : 'admin/blog/store') ?>" id="post-form">
  <?= csrf_field() ?>
  <div class="two">
    <div class="card">
      <label for="title">Title</label>
      <input type="text" id="title" name="title" required maxlength="255" value="<?= esc($val('title'), 'attr') ?>">

      <label for="slug">URL slug <span class="mute">(leave blank to <?= $editing ? 'keep the current one' : 'generate from the title' ?>)</span></label>
      <input type="text" id="slug" name="slug" maxlength="255" value="<?= esc(old('slug', ''), 'attr') ?>" placeholder="<?= esc($post['slug'] ?? 'my-post-title', 'attr') ?>">

      <label for="excerpt">Excerpt <span class="mute">(max 300 characters; used on the blog list and meta description)</span></label>
      <textarea id="excerpt" name="excerpt" maxlength="300"><?= esc($val('excerpt')) ?></textarea>

      <label>Body</label>
      <div id="editor" style="min-height:320px;background:#fff"><?= $val('content') ?></div>
      <input type="hidden" name="content" id="content">
      <p class="mute">Allowed: headings, paragraphs, lists, links, images, quotes, code and tables. Anything else is stripped on save.</p>
    </div>

    <div>
      <div class="card">
        <label for="status">Status</label>
        <select id="status" name="status">
          <?php foreach (['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'] as $k => $l): ?>
            <option value="<?= $k ?>" <?= $val('status', 'draft') === $k ? 'selected' : '' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= (int) $val('category_id', 0) === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (! $categories): ?><p class="mute">Create a <a href="<?= site_url('admin/categories') ?>">category</a> first.</p><?php endif; ?>
      </div>

      <div class="card">
        <h2>Tags</h2>
        <?php foreach ($allTags as $t): ?>
          <label style="font-weight:400;margin:.2rem 0"><input type="checkbox" name="tags[]" value="<?= $t['id'] ?>" <?= in_array((int) $t['id'], $checked, true) ? 'checked' : '' ?>> <?= esc($t['name']) ?></label>
        <?php endforeach; ?>
        <label for="new_tags">New tags (comma separated)</label>
        <input type="text" id="new_tags" name="new_tags" value="<?= esc(old('new_tags', ''), 'attr') ?>">
      </div>

      <div class="row">
        <button class="btn" type="submit"><?= $editing ? 'Save changes' : 'Create post' ?></button>
        <a class="btn ghost" href="<?= site_url('admin/blog') ?>">Cancel</a>
      </div>
    </div>
  </div>
</form>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
  (function () {
    var form = document.getElementById('post-form'), hidden = document.getElementById('content');
    if (!window.Quill) { // CDN blocked: fall back to a plain textarea so editing still works
      var ta = document.createElement('textarea'); ta.name = 'content'; ta.style.minHeight = '320px'; ta.value = document.getElementById('editor').innerHTML;
      document.getElementById('editor').replaceWith(ta); hidden.remove(); return;
    }
    var q = new Quill('#editor', {theme: 'snow', modules: {toolbar: [[{header: [2, 3, 4, false]}], ['bold', 'italic', 'underline'], [{list: 'ordered'}, {list: 'bullet'}], ['blockquote', 'code-block', 'link', 'image'], ['clean']]}});
    form.addEventListener('submit', function () { hidden.value = q.getText().trim() === '' && !q.root.querySelector('img') ? '' : q.root.innerHTML; });
  })();
</script>
<?= $this->endSection() ?>
