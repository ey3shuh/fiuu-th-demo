<?php
// STEP 2 — Checkout: customer enters contact details, then pays with Fiuu.
require __DIR__ . '/config.php';

$lines = cart_lines();
if (!$lines) {
    header('Location: cart.php');
    exit;
}

$errors = $_SESSION['checkout_errors'] ?? [];
$old    = $_SESSION['checkout_old'] ?? ['bill_name' => 'Somchai Jaidee', 'bill_email' => 'somchai@example.com', 'bill_mobile' => '0812345678'];
unset($_SESSION['checkout_errors'], $_SESSION['checkout_old']);

$title = t('checkout');
require __DIR__ . '/partials/header.php';
?>
<div class="container">
    <h1 class="page-title"><?= e(t('checkout')) ?></h1>
    <div class="layout-2">
        <form class="panel" method="POST" action="pay.php" id="checkout">
            <h2><?= e(t('contact_details')) ?></h2>
            <?php foreach ($errors as $err): ?>
                <div class="alert"><?= e(t($err)) ?></div>
            <?php endforeach; ?>

            <label for="bill_name"><?= e(t('full_name')) ?></label>
            <input type="text" id="bill_name" name="bill_name" value="<?= e($old['bill_name']) ?>" required maxlength="100" autocomplete="name">

            <label for="bill_email"><?= e(t('email')) ?></label>
            <input type="email" id="bill_email" name="bill_email" value="<?= e($old['bill_email']) ?>" required maxlength="100" autocomplete="email">

            <label for="bill_mobile"><?= e(t('phone')) ?></label>
            <input type="tel" id="bill_mobile" name="bill_mobile" value="<?= e($old['bill_mobile']) ?>" required maxlength="20" autocomplete="tel" placeholder="08x-xxx-xxxx">

            <div class="secure">🔒 <span><?= e(t('secure_note')) ?></span></div>
        </form>

        <aside class="panel">
            <h2><?= e(t('order_summary')) ?></h2>
            <?php foreach ($lines as $l): ?>
                <div class="sum-row">
                    <span><?= $l['qty'] ?> × <?= e(pname($l['p'])) ?></span>
                    <span><?= money($l['total']) ?></span>
                </div>
            <?php endforeach; ?>
            <?php require __DIR__ . '/partials/totals.php'; ?>

            <div class="methods">
                <span class="pill">VISA</span><span class="pill">Mastercard</span>
                <span class="pill">PromptPay</span><span class="pill">TrueMoney</span>
            </div>
            <small style="color:var(--muted)"><?= e(t('pay_methods')) ?></small>
            <p></p>
            <button class="btn btn-block" type="submit" form="checkout">🔒 <?= e(t('pay_with_fiuu')) ?> · <?= money(cart_total()) ?></button>
            <p></p>
            <a class="btn btn-outline btn-block" href="cart.php"><?= e(t('back_to_cart')) ?></a>
        </aside>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
