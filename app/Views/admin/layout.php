<?php
$user    = auth()->user();
$current = uri_string();
$nav     = [
    ['admin',            'Dashboard',  true],
    ['admin/blog',       'Posts',      true],
    ['admin/categories', 'Categories', true],
    ['admin/tags',       'Tags',       true],
    ['admin/media',      'Media',      true],
    ['admin/comments',   'Comments',   true],
    ['admin/enquiries',  'Enquiries',  true],
    ['admin/users',      'Users',      $user->can('users.edit')],
];
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= esc($pageTitle ?? 'Admin') ?> | EchoCrew Admin</title>
<style>
:root{--bg:#f6f6f3;--card:#fff;--ink:#16181d;--mute:#6b7280;--line:#e4e4de;--accent:#1f4fd8;--bad:#b42318;--ok:#067647}
*{box-sizing:border-box}body{margin:0;font:15px/1.5 system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:var(--bg);color:var(--ink);display:flex;min-height:100vh}
aside{width:210px;background:#101318;color:#cfd3da;padding:1.2rem 0;flex-shrink:0}
aside b{display:block;padding:0 1.2rem 1rem;color:#fff;font-size:1.05rem}
aside a{display:block;padding:.55rem 1.2rem;color:inherit;text-decoration:none}
aside a:hover,aside a.on{background:#1b2029;color:#fff}
main{flex:1;padding:1.6rem 2rem;min-width:0}
header.top{display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.2rem;flex-wrap:wrap}
h1{font-size:1.5rem;margin:0}h2{font-size:1.1rem;margin:0 0 .6rem}
.card{background:var(--card);border:1px solid var(--line);border-radius:10px;padding:1.1rem;margin-bottom:1.2rem}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:1rem;margin-bottom:1.2rem}
.stat{background:var(--card);border:1px solid var(--line);border-radius:10px;padding:1rem}.stat b{display:block;font-size:1.8rem}.stat span{color:var(--mute)}
table{width:100%;border-collapse:collapse}th,td{text-align:left;padding:.55rem .6rem;border-bottom:1px solid var(--line);vertical-align:top}th{font-size:.8rem;color:var(--mute);text-transform:uppercase;letter-spacing:.04em}
.tbl{overflow-x:auto}
a{color:var(--accent)}label{display:block;font-weight:600;margin:.8rem 0 .25rem}
input[type=text],input[type=email],input[type=password],input[type=search],select,textarea{width:100%;padding:.55rem .65rem;border:1px solid var(--line);border-radius:7px;font:inherit;background:#fff}
textarea{min-height:90px}
.btn{display:inline-block;background:var(--accent);color:#fff;border:0;border-radius:7px;padding:.5rem .95rem;font:inherit;cursor:pointer;text-decoration:none}
.btn[disabled]{opacity:.45;cursor:not-allowed}.btn.ghost{background:#fff;color:var(--ink);border:1px solid var(--line)}.btn.bad{background:var(--bad)}.btn.sm{padding:.25rem .6rem;font-size:.85rem}
form.inline{display:inline}.row{display:flex;gap:.6rem;flex-wrap:wrap;align-items:center}
.pill{display:inline-block;padding:.05rem .55rem;border-radius:99px;font-size:.78rem;background:#eceef2}
.pill.published,.pill.approved,.pill.closed{background:#d1fadf;color:var(--ok)}.pill.draft,.pill.new{background:#fef0c7;color:#93370d}.pill.archived,.pill.hidden{background:#eee;color:#555}.pill.contacted{background:#dbe6ff;color:#1b3fa0}
.alert{padding:.7rem .9rem;border-radius:8px;margin-bottom:1rem}.alert.ok{background:#d1fadf;color:var(--ok)}.alert.bad{background:#fee4e2;color:var(--bad)}
.mute{color:var(--mute)}.two{display:grid;grid-template-columns:2fr 1fr;gap:1.2rem}
@media(max-width:800px){body{flex-direction:column}aside{width:auto;display:flex;flex-wrap:wrap;padding:.4rem}aside b{padding:.5rem}.two{grid-template-columns:1fr}main{padding:1rem}}
</style>
</head>
<body>
<aside>
  <b>EchoCrew Admin</b>
  <?php foreach ($nav as [$path, $label, $show]): if (! $show) { continue; } ?>
    <a href="<?= site_url($path) ?>" class="<?= ($current === $path || ($path !== 'admin' && str_starts_with($current, $path))) ? 'on' : '' ?>"><?= esc($label) ?></a>
  <?php endforeach; ?>
  <a href="<?= base_url('blog') ?>" target="_blank" rel="noopener">View blog &#8599;</a>
  <a href="<?= site_url('logout') ?>">Log out (<?= esc($user->username) ?>)</a>
</aside>
<main>
  <header class="top"><h1><?= esc($pageTitle ?? '') ?></h1><div class="row"><?= $this->renderSection('actions') ?></div></header>
  <?php if (session('message')): ?><div class="alert ok" role="status"><?= esc(session('message')) ?></div><?php endif; ?>
  <?php if (session('error')): ?><div class="alert bad" role="alert"><?= esc(session('error')) ?></div><?php endif; ?>
  <?php if (session('errors')): ?><div class="alert bad" role="alert"><?php foreach ((array) session('errors') as $e): ?><div><?= esc($e) ?></div><?php endforeach; ?></div><?php endif; ?>
  <?= $this->renderSection('content') ?>
</main>
</body>
</html>
