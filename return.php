<?php
// STEP 4 — Fiuu sends the customer back here with the result.
// Always check skey before trusting the status.
require __DIR__ . '/config.php';

$r = $_POST + $_GET;
$required = ['tranID', 'orderid', 'status', 'domain', 'amount', 'currency', 'appcode', 'paydate', 'skey'];
$hasAll = !array_diff($required, array_keys($r));

$skeyOk  = $hasAll && hash_equals(make_skey($r), (string) $r['skey']);
$status  = $r['status'] ?? '';
$success = $skeyOk && $status === '00';
$pending = $skeyOk && $status === '22';

// Items for the receipt, if this browser placed the order
$order = $_SESSION['last_order'] ?? null;
if (!$order || $order['orderid'] !== ($r['orderid'] ?? '')) {
    $order = null;
}

if (!$hasAll)       [$icon, $cls, $head, $sub] = ['?', 'warn', 'noresult_title', null];
elseif (!$skeyOk)   [$icon, $cls, $head, $sub] = ['!', 'bad', 'unverified_title', 'unverified_sub'];
elseif ($success)   [$icon, $cls, $head, $sub] = ['✓', 'ok', 'paid_title', 'paid_sub'];
elseif ($pending)   [$icon, $cls, $head, $sub] = ['…', 'warn', 'pending_title', 'pending_sub'];
else                [$icon, $cls, $head, $sub] = ['✕', 'bad', 'failed_title', 'failed_sub'];

$title    = t($head);
$hideLang = true;
require __DIR__ . '/partials/header.php';
?>
<div class="container">
    <div class="panel center-card">
        <div class="status-icon <?= $cls ?>"><?= $icon ?></div>
        <h2><?= e(t($head)) ?></h2>
        <?php if ($sub): ?><p style="color:var(--muted)"><?= e(t($sub)) ?></p><?php endif; ?>
        <?php if ($hasAll && !$success && !empty($r['error_desc'])): ?>
            <p class="bad-text"><?= e($r['error_desc']) ?></p>
        <?php endif; ?>

        <?php if ($order && $success): ?>
            <div style="text-align:left;margin-top:16px">
            <?php foreach ($order['lines'] as $l): ?>
                <div class="sum-row"><span><?= $l['qty'] ?> × <?= e(pname($l['p'])) ?></span><span><?= money($l['total']) ?></span></div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($hasAll): ?>
        <table class="kv">
            <tr><td><?= e(t('order_id')) ?></td><td><?= e($r['orderid']) ?></td></tr>
            <tr><td><?= e(t('transaction_id')) ?></td><td><?= e($r['tranID']) ?></td></tr>
            <tr><td><?= e(t('amount')) ?></td><td><strong><?= e($r['currency']) ?> <?= e($r['amount']) ?></strong></td></tr>
            <tr><td><?= e(t('payment_method')) ?></td><td><?= e($r['channel'] ?? '-') ?></td></tr>
            <tr><td><?= e(t('paid_at')) ?></td><td><?= e($r['paydate']) ?></td></tr>
            <tr><td><?= e(t('status')) ?></td><td><?= e($status) ?> <small style="color:var(--muted)">(00 = success, 11 = failed, 22 = pending)</small></td></tr>
            <tr><td>skey</td><td class="<?= $skeyOk ? 'ok-text' : 'bad-text' ?>"><?= $skeyOk ? '✓ ' . e(t('verified')) : '✕ ' . e(t('not_verified')) ?></td></tr>
        </table>
        <?php endif; ?>

        <div class="actions">
            <?php if ($success): ?>
                <a class="btn" href="index.php?paid=<?= urlencode($r['orderid']) ?>"><?= e(t('continue_shopping')) ?></a>
            <?php else: ?>
                <a class="btn" href="checkout.php"><?= e(t('try_again')) ?></a>
                <a class="btn btn-outline" href="index.php"><?= e(t('continue_shopping')) ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
