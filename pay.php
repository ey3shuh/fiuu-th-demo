<?php
// STEP 3 — Server side: validate customer, total the cart, build the vcode,
// then send the customer to Fiuu's hosted payment page.
require __DIR__ . '/config.php';

$lines = cart_lines();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$lines) {
    header('Location: cart.php');
    exit;
}

$customer = [
    'bill_name'   => trim((string) ($_POST['bill_name'] ?? '')),
    'bill_email'  => trim((string) ($_POST['bill_email'] ?? '')),
    'bill_mobile' => preg_replace('/[\s-]/', '', (string) ($_POST['bill_mobile'] ?? '')),
];

$errors = [];
if (in_array('', $customer, true))                                   $errors[] = 'err_required';
elseif (!filter_var($customer['bill_email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'err_email';
elseif (!preg_match('/^\+?\d{9,15}$/', $customer['bill_mobile']))    $errors[] = 'err_phone';

if ($errors) {
    $_SESSION['checkout_errors'] = $errors;
    $_SESSION['checkout_old']    = $customer;
    header('Location: checkout.php', true, 303);
    exit;
}

// Amount comes from the server-side price list, never from the browser
$amount  = number_format(cart_total() / 100, 2, '.', '');
$orderid = 'DEMO' . date('ymdHis');
$desc    = implode(', ', array_map(fn($l) => $l['qty'] . 'x ' . pname($l['p']), $lines));

$fields = $customer + [
    'amount'    => $amount,
    'orderid'   => $orderid,
    'currency'  => 'THB',
    'langcode'  => LANG,   // open Fiuu's page in the shop's language (en / th)
    'country'   => 'TH',
    'bill_desc' => mb_strimwidth($desc, 0, 200, '…'),
    'returnurl' => base_url() . '/return.php?lang=' . LANG,
    'cancelurl' => base_url() . '/checkout.php',
    'vcode'     => make_vcode($amount, $orderid),
];

$_SESSION['last_order'] = ['orderid' => $orderid, 'lines' => $lines, 'total' => cart_total()];

$title    = t('redirect_title');
$hideLang = true;
require __DIR__ . '/partials/header.php';
?>
<div class="container">
    <div class="panel center-card">
        <div class="spinner"></div>
        <h2><?= e(t('redirect_title')) ?></h2>
        <p style="color:var(--muted)"><?= e(t('redirect_sub')) ?></p>

        <!-- This form is the "trigger": the browser sends these fields to Fiuu.
             GET (fields in the URL) so the EN/TH switch on Fiuu's page keeps the order —
             that switch rebuilds its link from the URL only. -->
        <form id="toFiuu" method="GET" action="<?= e(PAYMENT_URL) ?>">
            <?php foreach ($fields as $k => $v): ?>
                <input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>">
            <?php endforeach; ?>
            <button class="btn btn-block" type="submit">🔒 <?= e(t('proceed')) ?> · <?= money(cart_total()) ?></button>
        </form>

        <details class="tech">
            <summary><?= e(t('tech_details')) ?></summary>
            <table class="kv">
                <tr><td><?= e(t('order_id')) ?></td><td><?= e($orderid) ?></td></tr>
                <tr><td><?= e(t('amount')) ?></td><td>THB <?= e($amount) ?></td></tr>
                <tr><td>bill_desc</td><td><?= e($fields['bill_desc']) ?></td></tr>
            </table>
        </details>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
