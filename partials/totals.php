<?php
// Subtotal / VAT / total rows. Thai menu prices include 7% VAT, so VAT = total * 7/107.
$total = cart_total();
$vat   = (int) round($total * 7 / 107);
?>
<div class="sum-row"><span><?= e(t('subtotal')) ?></span><span><?= money($total - $vat) ?></span></div>
<div class="sum-row"><span><?= e(t('vat_line')) ?></span><span><?= money($vat) ?></span></div>
<div class="sum-row total"><span><?= e(t('total')) ?></span><span><?= money($total) ?></span></div>
