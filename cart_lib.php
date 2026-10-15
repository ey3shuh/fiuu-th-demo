<?php
// Cart stored in the session as [productId => qty]. Totals always come from products.php.

function products(): array
{
    static $products;
    return $products ??= require __DIR__ . '/products.php';
}

function cart(): array
{
    return array_intersect_key($_SESSION['cart'] ?? [], products());
}

function cart_set(string $id, int $qty): void
{
    if (!isset(products()[$id])) {
        return;
    }
    if ($qty <= 0) {
        unset($_SESSION['cart'][$id]);
    } else {
        $_SESSION['cart'][$id] = min($qty, 20);
    }
}

function cart_lines(): array
{
    $lines = [];
    foreach (cart() as $id => $qty) {
        $p = products()[$id];
        $lines[] = ['id' => $id, 'p' => $p, 'qty' => $qty, 'total' => $p['price'] * $qty];
    }
    return $lines;
}

function cart_total(): int
{
    return array_sum(array_column(cart_lines(), 'total'));
}

function cart_count(): int
{
    return array_sum(cart());
}

function pname(array $p): string
{
    return $p['name'][LANG];
}

// 7500 -> "฿75.00"
function money(int $satang): string
{
    return '฿' . number_format($satang / 100, 2);
}

// One-time message shown on the next page
function flash(?string $msg = null): ?string
{
    if ($msg !== null) {
        $_SESSION['flash'] = $msg;
        return null;
    }
    $m = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $m;
}
