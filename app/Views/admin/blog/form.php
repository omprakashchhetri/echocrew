<?php
$editing = $post !== null;
$val     = static fn (string $k, $default = '') => old($k, $post[$k] ?? $default);
$checked = array_map('intval', (array) old('tags', $postTagIds));
?>
<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<form method="post" action="<?= site_url($editing ? 'admin/blog/update/' . $post['id'] : 'admin/blog/store') ?>" id="post-form" enctype="multipart/form-data">
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
        <h2>Cover image</h2>
        <?php if (! empty($post['cover_image'])): ?>
          <img src="<?= base_url($post['cover_image']) ?>" alt="" style="width:100%;border-radius:8px;margin-bottom:.5rem">
          <label style="font-weight:400"><input type="checkbox" name="remove_cover" value="1"> Remove cover image</label>
        <?php endif; ?>
        <label for="cover"><?= ! empty($post['cover_image']) ? 'Replace image' : 'Upload image' ?> <span class="mute">(JPG, PNG, WebP or GIF, max <?= esc(\App\Libraries\ImageUploader::limitLabel()) ?>; 16:9 works best)</span></label>
        <input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/webp,image/gif">
        <label for="cover_alt">Alt text <span class="mute">(describe the image)</span></label>
        <input type="text" id="cover_alt" name="cover_alt" maxlength="200" value="<?= esc($val('cover_alt'), 'attr') ?>">
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
    var MAX_BYTES = <?= \App\Libraries\ImageUploader::limitBytes() ?>;
    var upUrl = <?= json_encode(site_url('admin/media/upload')) ?>, tokenField = form.querySelector('input[name="<?= csrf_token() ?>"]');
    function pickImage() {
      var input = document.createElement('input'); input.type = 'file'; input.accept = 'image/jpeg,image/png,image/webp,image/gif';
      input.onchange = function () {
        var f = input.files[0]; if (!f) return;
        if (f.size > MAX_BYTES) { alert('That image is larger than ' + <?= json_encode(\App\Libraries\ImageUploader::limitLabel()) ?> + '.'); return; }
        var fd = new FormData(); fd.append('image', f); fd.append(tokenField.name, tokenField.value);
        fetch(upUrl, {method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin'})
          .then(function (r) { return r.json(); })
          .then(function (j) {
            if (j.csrf) tokenField.value = j.csrf;
            if (j.error) { alert(j.error); return; }
            var range = q.getSelection(true); q.insertEmbed(range.index, 'image', j.url); q.setSelection(range.index + 1);
          })
          .catch(function () { alert('Image upload failed.'); });
      };
      input.click();
    }
    var q = new Quill('#editor', {theme: 'snow', modules: {toolbar: {container: [[{header: [2, 3, 4, false]}], ['bold', 'italic', 'underline'], [{list: 'ordered'}, {list: 'bullet'}], ['blockquote', 'code-block', 'link', 'image'], ['clean']], handlers: {image: pickImage}}}});
    var coverInput = document.getElementById('cover');
    if (coverInput) coverInput.addEventListener('change', function () {
      if (coverInput.files[0] && coverInput.files[0].size > MAX_BYTES) { alert('That image is larger than ' + <?= json_encode(\App\Libraries\ImageUploader::limitLabel()) ?> + '. Choose a smaller one.'); coverInput.value = ''; }
    });
    // Pasted or dropped images: upload them instead of embedding base64 (which the sanitiser would strip).
    function uploadFile(f) {
      if (f.size > MAX_BYTES) { alert('That image is larger than ' + <?= json_encode(\App\Libraries\ImageUploader::limitLabel()) ?> + '.'); return; }
      var fd = new FormData(); fd.append('image', f); fd.append(tokenField.name, tokenField.value);
      fetch(upUrl, {method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin'})
        .then(function (r) { return r.json(); })
        .then(function (j) { if (j.csrf) tokenField.value = j.csrf; if (j.error) { alert(j.error); return; } var range = q.getSelection(true); q.insertEmbed(range.index, 'image', j.url); })
        .catch(function () { alert('Image upload failed.'); });
    }
    q.root.addEventListener('paste', function (e) {
      var items = (e.clipboardData && e.clipboardData.files) || [];
      if (items.length && /^image\//.test(items[0].type)) { e.preventDefault(); e.stopPropagation(); uploadFile(items[0]); }
    }, true);
    q.root.addEventListener('drop', function (e) {
      var files = (e.dataTransfer && e.dataTransfer.files) || [];
      if (files.length && /^image\//.test(files[0].type)) { e.preventDefault(); e.stopPropagation(); uploadFile(files[0]); }
    }, true);
    form.addEventListener('submit', function () { hidden.value = q.getText().trim() === '' && !q.root.querySelector('img') ? '' : q.root.innerHTML; });
  })();
</script>
<?= $this->endSection() ?>
