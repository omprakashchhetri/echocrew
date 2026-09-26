<?php
/** @var \Config\Seo $seo */
$seo         = config('Seo');
$pageTitle   = $title ?? ($seo->brand . ' | ' . $seo->tagline);
$pageDesc    = $description ?? 'EchoCrew builds custom websites, CRM systems, management software, e-commerce platforms, integrations, automation and AI-powered digital solutions around real business workflows.';
$pageCanon   = $canonical ?? current_url();
$pageImage   = $ogImage ?? base_url('assets/img/ec/og-echocrew.jpg');
$assetVer    = $assetVersion ?? '2';
?>
<!DOCTYPE html>
<html lang="en-IN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FAFAF7">
    <script>
        // Hide animated elements before first paint only when motion will actually run.
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) document.documentElement.classList.add('ec-motion');
    </script>

    <title><?= esc($pageTitle) ?></title>
    <meta name="description" content="<?= esc($pageDesc) ?>">
    <link rel="canonical" href="<?= esc($pageCanon, 'url') ?>">
    <meta name="robots"
        content="<?= esc($robots ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1') ?>">
    <meta name="author" content="<?= esc($seo->brand) ?>">

    <meta property="og:type" content="<?= esc($ogType ?? 'website') ?>">
    <meta property="og:site_name" content="<?= esc($seo->brand) ?>">
    <meta property="og:locale" content="<?= esc($seo->locale) ?>">
    <meta property="og:title" content="<?= esc($pageTitle) ?>">
    <meta property="og:description" content="<?= esc($pageDesc) ?>">
    <meta property="og:url" content="<?= esc($pageCanon, 'url') ?>">
    <meta property="og:image" content="<?= esc($pageImage, 'url') ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($pageTitle) ?>">
    <meta name="twitter:description" content="<?= esc($pageDesc) ?>">
    <meta name="twitter:image" content="<?= esc($pageImage, 'url') ?>">

    <link rel="icon" type="image/png" sizes="64x64" href="<?= base_url('assets/img/ec/favicon-64.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/img/ec/apple-touch-icon.png') ?>">

    <link rel="preload" as="font" type="font/woff2" href="<?= base_url('assets/fonts/ClashDisplay-Semibold.woff2') ?>" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="<?= base_url('assets/fonts/Platform-Regular.woff2') ?>" crossorigin>
    <link rel="stylesheet" href="<?= base_url('assets/css/echocrew.css') ?>?v=<?= esc($assetVer, 'attr') ?>">

    <?= $this->include('partials/schema') ?>
</head>

<body class="<?= esc($bodyClass ?? '', 'attr') ?>">

    <a class="ec-skip" href="#ec-main">Skip to content</a>

    <?= $this->include('partials/nav') ?>

    <main id="ec-main">
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('partials/footer') ?>

    <script src="<?= base_url('assets/js/plugin.js') ?>" defer></script>
    <script src="<?= base_url('assets/js/echocrew.js') ?>?v=<?= esc($assetVer, 'attr') ?>" defer></script>
</body>

</html>
