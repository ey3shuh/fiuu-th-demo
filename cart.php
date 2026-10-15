<?php
// Cart page. POST actions (add / inc / dec / remove) change the cart, then redirect back.
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id  = (string) ($_POST['id'] ?? '');
    $qty = cart()[$id] ?? 0;

    switch ($_POST['action'] ?? '') {
        case 'add':
            cart_set($id, $qty + 1);
            if (isset(products()[$id])) {
                flash(t('added', pname(products()[$id])));
            }
            break;
        case 'inc':    cart_set($id, $qty + 1); break;
        case 'dec':    cart_set($id, $qty - 1); break;
        case 'remove': cart_set($id, 0); break;
    }

    // Only redirect back to our own pages
    $back = (string) ($_POST['back'] ?? 'cart.php');
    if (!preg_match('/^(index|cart)\.php/', $back)) {
        $back = 'cart.php';
    }
    header('Location: ' . $back, true, 303);
    exit;
}

$lines = cart_lines();
$title = t('cart_title');
require __DIR__ . '/partials/header.php';
?>
<div class="container">
    <h1 class="page-title"><?= e(t('cart_title')) ?></h1>

    <?php if (!$lines): ?>
        <div class="panel empty">
            <div class="big">🛒</div>
            <p><?= e(t('cart_empty')) ?></p>
            <a class="btn" href="index.php#menu"><?= e(t('browse_menu')) ?></a>
        </div>
    <?php else: ?>
    <div class="layout-2">
        <div class="panel">
            <?php foreach ($lines as $l): ?>
            <div class="line">
                <div class="line-img cat-<?= $l['p']['cat'] ?>"><?= $l['p']['emoji'] ?></div>
                <div class="line-info">
                    <strong><?= e(pname($l['p'])) ?></strong>
                    <small><?= money($l['p']['price']) ?></small>
                    <form method="POST" style="margin-top:4px">
                        <input type="hidden" name="id" value="<?= e($l['id']) ?>">
                        <button class="link-btn" name="action" value="remove"><?= e(t('remove')) ?></button>
                    </form>
                </div>
                <form method="POST" class="qty">
                    <input type="hidden" name="id" value="<?= e($l['id']) ?>">
                    <button name="action" value="dec" aria-label="-">−</button>
                    <span><?= $l['qty'] ?></span>
                    <button name="action" value="inc" aria-label="+">+</button>
                </form>
                <div class="line-total"><?= money($l['total']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <aside class="panel">
            <h2><?= e(t('order_summary')) ?></h2>
            <?php require __DIR__ . '/partials/totals.php'; ?>
            <p></p>
            <a class="btn btn-block" href="checkout.php"><?= e(t('checkout')) ?> →</a>
            <p></p>
            <a class="btn btn-outline btn-block" href="index.php#menu"><?= e(t('continue_shopping')) ?></a>
        </aside>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
