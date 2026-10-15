<?php
// Loads credentials from .env. Only server-side files include this —
// the keys never reach the customer's browser.

session_start();

// Simple .env reader: NAME=value per line, # for comments, values taken as-is
$env = [];
if (file_exists(__DIR__ . '/.env')) {
    foreach (file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        $env[trim($name)] = trim($value);
    }
}

define('MODE', ($env['FIUU_MODE'] ?? 'mock') === 'live' ? 'live' : 'mock');

if (MODE === 'live') {
    foreach (['FIUU_MERCHANT_ID', 'FIUU_VERIFY_KEY', 'FIUU_SECRET_KEY', 'FIUU_PAYMENT_URL'] as $required) {
        if (empty($env[$required])) {
            http_response_code(500);
            exit("Config error: $required is missing or empty in .env (FIUU_MODE=live).");
        }
    }
    if (str_contains($env['FIUU_PAYMENT_URL'], '?')) {
        http_response_code(500);
        exit("Config error: FIUU_PAYMENT_URL must be the base URL only, e.g. https://<host>/RMS/pay/ (no merchant ID, no ?amount=...).");
    }
    define('MERCHANT_ID', $env['FIUU_MERCHANT_ID'] ?? '');
    define('VERIFY_KEY',  $env['FIUU_VERIFY_KEY'] ?? '');
    define('SECRET_KEY',  $env['FIUU_SECRET_KEY'] ?? '');
    define('PAYMENT_URL', rtrim($env['FIUU_PAYMENT_URL'] ?? '', '/') . '/' . MERCHANT_ID . '/');
} else {
    // Fake values so the full flow works before real credentials arrive
    define('MERCHANT_ID', 'demoshop_Dev');
    define('VERIFY_KEY',  'mock-verify-key');
    define('SECRET_KEY',  'mock-secret-key');
    define('PAYMENT_URL', 'mock_gateway.php');
}

define('HASH_ALGO', strtolower($env['FIUU_HASH'] ?? 'md5'));

// Base URL of this demo site, e.g. http://localhost:8000
function base_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $scheme . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
}

// vcode = seal on the outgoing request: hash(amount + merchantID + orderid + verify_key)
function make_vcode(string $amount, string $orderid): string
{
    return hash(HASH_ALGO, $amount . MERCHANT_ID . $orderid . VERIFY_KEY);
}

// skey = seal on Fiuu's reply, proves the result really came from Fiuu
function make_skey(array $r): string
{
    $key0 = md5($r['tranID'] . $r['orderid'] . $r['status'] . $r['domain'] . $r['amount'] . $r['currency']);
    return md5($r['paydate'] . $r['domain'] . $key0 . $r['appcode'] . SECRET_KEY);
}

function e($s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

require __DIR__ . '/lang.php';
require __DIR__ . '/cart_lib.php';
