<?php
// MOCK ONLY — pretends to be Fiuu's hosted payment page so the flow can be
// tested before real credentials arrive. Not used when FIUU_MODE=live.
require __DIR__ . '/config.php';

if (MODE === 'live') {
    http_response_code(404);
    exit('Mock gateway is disabled in live mode.');
}

// Customer clicked Pay / Fail: send them back to the shop's return URL with a signed result
if (isset($_POST['result'])) {
    $ok = $_POST['result'] === 'success';
    $reply = [
        'tranID'     => (string) random_int(10000000, 99999999),
        'orderid'    => $_POST['orderid'],
        'status'     => $ok ? '00' : '11',
        'domain'     => MERCHANT_ID,
        'amount'     => $_POST['amount'],
        'currency'   => $_POST['currency'],
        'appcode'    => $ok ? 'APP' . random_int(100, 999) : '',
        'paydate'    => date('Y-m-d H:i:s'),
        'channel'    => 'Credit',
        'error_code' => $ok ? '' : 'MOCK_DECLINED',
        'error_desc' => $ok ? '' : 'Card declined (mock)',
    ];
    $reply['skey'] = make_skey($reply);
    ?>
    <!doctype html><html><body onload="document.forms[0].submit()">
    <form method="POST" action="<?= e($_POST['returnurl']) ?>">
        <?php foreach ($reply as $k => $v): ?>
            <input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>">
        <?php endforeach; ?>
    </form></body></html>
    <?php
    exit;
}

// First arrival from the shop: check the vcode like the real gateway does
$in = $_GET + $_POST;   // shop sends the order by GET
$vcodeOk = hash_equals(make_vcode($in['amount'] ?? '', $in['orderid'] ?? ''), $in['vcode'] ?? '');
$total   = trim(($in['currency'] ?? '') . ' ' . ($in['amount'] ?? ''));
?>
<!doctype html>
<html lang="<?= LANG ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fiuu Payment (Mock)</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="demo-bar">MOCK FIUU PAYMENT PAGE</div>
<div class="gw-top"><div class="container"><strong>fiuu</strong> · <?= e(t('gw_title')) ?></div></div>
<div class="container">
    <div class="panel gw-card center-card" style="text-align:left">
        <table class="kv">
            <tr><td><?= e(t('gw_merchant')) ?></td><td><?= e(MERCHANT_ID) ?></td></tr>
            <tr><td><?= e(t('order_id')) ?></td><td><?= e($in['orderid'] ?? '') ?></td></tr>
            <tr><td><?= e(t('gw_desc')) ?></td><td><?= e($in['bill_desc'] ?? '') ?></td></tr>
            <tr><td><?= e(t('full_name')) ?></td><td><?= e($in['bill_name'] ?? '') ?></td></tr>
            <tr><td><?= e(t('total')) ?></td><td><strong><?= e($total) ?></strong></td></tr>
            <tr><td>vcode</td><td class="<?= $vcodeOk ? 'ok-text' : 'bad-text' ?>"><?= $vcodeOk ? '✓ ' . e(t('gw_vcode_ok')) : '✕ ' . e(t('gw_vcode_bad')) ?></td></tr>
        </table>

        <?php if ($vcodeOk): ?>
        <form method="POST">
            <?php foreach (['orderid', 'amount', 'currency', 'returnurl'] as $k): ?>
                <input type="hidden" name="<?= $k ?>" value="<?= e($in[$k] ?? '') ?>">
            <?php endforeach; ?>
            <label><?= e(t('gw_card')) ?></label>
            <input type="text" value="4111 1111 1111 1111">
            <p></p>
            <button class="btn btn-block" type="submit" name="result" value="success"><?= e(t('gw_pay', $total)) ?></button>
            <p></p>
            <button class="btn btn-danger btn-block" type="submit" name="result" value="fail"><?= e(t('gw_fail')) ?></button>
        </form>
        <?php endif; ?>
        <p style="font-size:12px;color:var(--muted);margin-top:16px"><?= e(t('gw_note')) ?></p>
    </div>
</div>
</body>
</html>
