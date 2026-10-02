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
        <p style="margin:.5rem 0 0"><button class="btn ghost sm" type="button" id="cover-library">Choose from library</button></p>
        <input type="hidden" name="cover_existing" id="cover_existing" value="">
        <div id="cover-picked" class="mute" style="margin-top:.5rem;display:none"></div>
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

<div id="lib-modal" hidden style="position:fixed;inset:0;z-index:100;background:rgba(10,12,16,.6);display:none;align-items:center;justify-content:center;padding:1rem">
  <div class="card" style="width:min(900px,100%);max-height:88vh;overflow:auto;margin:0" role="dialog" aria-modal="true" aria-label="Media library">
    <div class="row" style="justify-content:space-between;margin-bottom:.8rem">
      <h2 style="margin:0">Media library</h2>
      <button type="button" class="btn ghost sm" id="lib-close">Close</button>
    </div>
    <div id="lib-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:.6rem"></div>
    <p id="lib-empty" class="mute" style="display:none">No images yet. Upload one from the Media page or the editor.</p>
    <p style="text-align:center;margin:1rem 0 0"><button type="button" class="btn ghost sm" id="lib-more" style="display:none">Load more</button></p>
  </div>
</div>
<script>
  // Media library picker: window.ecPickMedia(function (item) { ... }) where item = {url, path, w, h}
  (function () {
    var modal = document.getElementById('lib-modal'), grid = document.getElementById('lib-grid'),
        more = document.getElementById('lib-more'), empty = document.getElementById('lib-empty'),
        listUrl = <?= json_encode(site_url('admin/media/list')) ?>, page = 0, pages = 1, onPick = null;

    function close() { modal.style.display = 'none'; onPick = null; }
    function load() {
      fetch(listUrl + '?page=' + (page + 1), {credentials: 'same-origin'}).then(function (r) { return r.json(); }).then(function (j) {
        page = j.page; pages = j.pages;
        j.items.forEach(function (it) {
          var b = document.createElement('button'); b.type = 'button';
          b.style.cssText = 'padding:0;border:1px solid #e4e4de;border-radius:8px;overflow:hidden;background:#fff;cursor:pointer';
          b.title = it.w + '\u00d7' + it.h;
          var im = document.createElement('img'); im.src = it.url; im.alt = ''; im.loading = 'lazy'; im.style.cssText = 'width:100%;aspect-ratio:1;object-fit:cover;display:block';
          b.appendChild(im);
          b.addEventListener('click', function () { var cb = onPick; close(); if (cb) cb(it); });
          grid.appendChild(b);
        });
        empty.style.display = grid.children.length ? 'none' : 'block';
        more.style.display = page < pages ? 'inline-block' : 'none';
      }).catch(function () { alert('Could not load the media library.'); });
    }
    window.ecPickMedia = function (cb) {
      onPick = cb; grid.innerHTML = ''; page = 0; modal.style.display = 'flex'; load();
    };
    more.addEventListener('click', load);
    document.getElementById('lib-close').addEventListener('click', close);
    modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && modal.style.display === 'flex') close(); });

    var coverBtn = document.getElementById('cover-library'), existing = document.getElementById('cover_existing'), picked = document.getElementById('cover-picked');
    if (coverBtn) coverBtn.addEventListener('click', function () {
      window.ecPickMedia(function (it) {
        existing.value = it.path;
        picked.style.display = 'block';
        picked.innerHTML = '<img src="' + it.url + '" alt="" style="width:100%;border-radius:8px;display:block;margin-bottom:.3rem">Selected from library. Save the post to apply. <a href="#" id="cover-unpick">Undo</a>';
        document.getElementById('cover-unpick').addEventListener('click', function (e) { e.preventDefault(); existing.value = ''; picked.style.display = 'none'; });
      });
    });
  })();
</script>

<link href="<?= base_url("assets/vendor/quill/quill.snow.css") ?>" rel="stylesheet">
<script src="<?= base_url("assets/vendor/quill/quill.js") ?>"></script>
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
    var tb = q.getModule('toolbar').container, grp = document.createElement('span');
    grp.className = 'ql-formats';
    grp.innerHTML = '<button type="button" id="ql-library" style="width:auto;padding:0 8px;font-size:13px" title="Insert image from library">Library</button>';
    tb.appendChild(grp);
    document.getElementById('ql-library').addEventListener('click', function () {
      var range = q.getSelection(true);
      window.ecPickMedia(function (it) { q.insertEmbed(range.index, 'image', it.url); q.setSelection(range.index + 1); });
    });

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
