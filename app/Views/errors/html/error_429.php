<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Too many requests</title>
<style>body{font:16px/1.6 system-ui,sans-serif;background:#FAFAF7;color:#111827;display:grid;place-items:center;min-height:100vh;margin:0;padding:1.5rem}main{max-width:460px}h1{font-size:1.6rem;margin:0 0 .6rem}a{color:#063B3B}</style>
</head>
<body><main>
<h1>Slow down a little</h1>
<p>We received too many requests from your connection. Please wait about <?= (int) ($wait ?? 60) ?> seconds and try again.</p>
<p><a href="/">Back to the site</a></p>
</main></body>
</html>
