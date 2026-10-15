# Baan Kafe — Fiuu Demo Shop

A demo café shop for the BOT site visit. It shows how a customer orders online and gets sent to the Fiuu hosted payment page. English / Thai.

## Setup

Needs **PHP 8.0+** with `mbstring` (check with `php -v` and `php -m | grep mbstring`).

```bash
# 1. Go into the folder
cd fiuu-demo-shop

# 2. Create your own .env from the example
cp .env.example .env

# 3. Start the server
php -S localhost:8000
```

Then open http://localhost:8000

That runs in **mock mode** (fake Fiuu page, no keys needed). For the real gateway, see Modes below.

## Modes (set in `.env`)

- `FIUU_MODE=mock`: fake Fiuu page on your laptop, no keys needed
- `FIUU_MODE=live`: real Fiuu gateway. Fill in `FIUU_VERIFY_KEY` and `FIUU_SECRET_KEY` (get them privately from the team), then restart the server.

All five values (merchant ID, both keys, URL, hash) must belong to the same merchant on the same server, or Fiuu rejects the payment.

## Flow

1. `index.php`: shop menu (EN / ไทย switch in the header), **Add to cart**
2. `cart.php`: change quantities, see subtotal + 7% VAT (included in prices)
3. `checkout.php`: customer enters name, email, mobile
4. `pay.php`: server totals the cart, builds the `vcode` and sends the customer to Fiuu (GET, so Fiuu's EN/TH switch keeps the order)
5. Fiuu hosted page (`mock_gateway.php` in mock mode), where the customer pays
6. `return.php`: Fiuu sends the customer back, and the server checks the `skey` before showing the receipt

Text for both languages is in `lang.php`. The menu and prices are in `products.php` (in satang, so `100` = ฿1.00).

## Troubleshooting

| You see | Fix |
|---|---|
| "Not Found" for every page | Server started from the wrong folder. Run `php -S localhost:8000 -t /path/to/fiuu-demo-shop` |
| "Config error: … missing" | A value is empty in `.env` while `FIUU_MODE=live` |
| HTTP 401 from Fiuu | Merchant ID doesn't exist on that gateway |
| "vcode unmatched" | Wrong Verify Key, or `FIUU_HASH` doesn't match the merchant's setting |

## Security

- `.env` holds the keys and is git-ignored. Never commit it or post it in a group chat.
- Prices come from `products.php` on the server, so the customer can't change the amount.
- `vcode` = hash(amount + merchantID + orderid + verify_key), which seals the request to Fiuu
- `skey` = md5(paydate + domain + key0 + appcode + secret_key), which proves the result came from Fiuu
