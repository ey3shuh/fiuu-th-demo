<?php
// Expects: $title (string). Optional: $hideLang (bool) for pages that can't be reloaded by GET.
$hideLang = $hideLang ?? false;
?>
<!doctype html>
<html lang="<?= LANG ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · <?= e(t('brand')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>☕</text></svg>">
</head>
<body>
<div class="demo-bar">
    DEMO · <?= MODE === 'live' ? 'LIVE — ' . e(parse_url(PAYMENT_URL, PHP_URL_HOST)) : 'MOCK GATEWAY' ?>
</div>
<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="index.php">
            <span class="logo-mark">☕</span>
            <span>
                <strong><?= e(t('brand')) ?></strong>
                <small><?= e(t('tagline')) ?></small>
            </span>
        </a>
        <nav class="nav">
            <a href="index.php#menu"><?= e(t('nav_menu')) ?></a>
            <?php if (!$hideLang): ?>
            <span class="lang-switch">
                <a href="<?= e(lang_url('en')) ?>" class="<?= LANG === 'en' ? 'active' : '' ?>">EN</a>
                <a href="<?= e(lang_url('th')) ?>" class="<?= LANG === 'th' ? 'active' : '' ?>">ไทย</a>
            </span>
            <?php endif; ?>
            <a class="cart-link" href="cart.php" aria-label="<?= e(t('nav_cart')) ?>">
                🛒 <span class="cart-label"><?= e(t('nav_cart')) ?></span>
                <?php if (cart_count() > 0): ?><span class="cart-count"><?= cart_count() ?></span><?php endif; ?>
            </a>
        </nav>
    </div>
</header>
<?php if ($msg = flash()): ?>
<div class="toast">✓ <?= e($msg) ?></div>
<?php endif; ?>
<main>
