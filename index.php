<?php
// STEP 1 — Shop page: browse the menu and add items to the cart. No keys here.
require __DIR__ . '/config.php';

// Back from a successful payment: empty the cart.
// (Done here, not on return.php, because browsers often don't send the
// session cookie on the cross-site POST coming back from Fiuu.)
if (isset($_GET['paid']) && $_GET['paid'] === ($_SESSION['last_order']['orderid'] ?? null)) {
    unset($_SESSION['cart'], $_SESSION['last_order']);
}

$cats = ['all', 'coffee', 'tea', 'bakery'];
$cat  = in_array($_GET['cat'] ?? '', $cats, true) ? $_GET['cat'] : 'all';
$list = array_filter(products(), fn($p) => $cat === 'all' || $p['cat'] === $cat);

$title = t('nav_menu');
require __DIR__ . '/partials/header.php';
?>
<section class="hero">
    <div class="container">
        <h1><?= e(t('hero_title')) ?></h1>
        <p><?= e(t('hero_sub')) ?></p>
        <a class="btn btn-light" href="#menu"><?= e(t('hero_cta')) ?> →</a>
        <div class="perks">
            <span>⏱ <?= e(t('perk_pickup')) ?></span>
            <span>🌱 <?= e(t('perk_beans')) ?></span>
            <span>🔒 <?= e(t('perk_secure')) ?></span>
        </div>
    </div>
</section>

<section class="section" id="menu">
    <div class="container">
        <h2><?= e(t('our_menu')) ?></h2>
        <div class="tabs">
            <?php foreach ($cats as $c): ?>
                <a class="tab <?= $c === $cat ? 'active' : '' ?>" href="?cat=<?= $c ?>#menu"><?= e(t('cat_' . $c)) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="grid">
        <?php foreach ($list as $id => $p): ?>
            <article class="product">
                <div class="product-img cat-<?= $p['cat'] ?>"><?= $p['emoji'] ?></div>
                <div class="product-body">
                    <h3><?= e(pname($p)) ?></h3>
                    <p><?= e($p['desc'][LANG]) ?></p>
                    <div class="product-foot">
                        <span class="price"><?= money($p['price']) ?></span>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="id" value="<?= e($id) ?>">
                            <input type="hidden" name="back" value="index.php?cat=<?= $cat ?>#menu">
                            <button class="btn" type="submit">+ <?= e(t('add_to_cart')) ?></button>
                        </form>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
