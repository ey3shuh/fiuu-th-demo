# ☕ Baan Kafe — Fiuu TH Payment Demo

A demo café website that shows how a customer pays through the **Fiuu hosted payment page**. Built for the BOT (Bank of Thailand) site visit.

The customer picks drinks, checks out, gets sent to Fiuu to pay (card, PromptPay QR, mobile banking, e-wallet), and comes back to the café to see the result. The site works in **English and Thai**.

```
Customer → Baan Kafe (menu → cart → checkout) → Fiuu payment page → Baan Kafe (result)
```

---

## 📋 Prerequisites

You need **3 things** on your computer:

| | What | Version | Check with |
|---|---|---|---|
| 1 | **PHP** | 8.0 or higher | `php -v` |
| 2 | **PHP mbstring extension** | (comes with PHP) | `php -m \| grep mbstring` |
| 3 | **Git** | any | `git --version` |

Plus:
- A web browser (Chrome, Edge, Firefox)
- An internet connection (for the live Fiuu gateway and the font)
- For **live mode** only: the merchant's **Verify Key** and **Secret Key** (ask the team privately)

No database, Composer, Node.js or Docker needed.

### Installing PHP and Git

<details>
<summary><b>🪟 Windows (with WSL / Ubuntu) — recommended</b></summary>

1. If you don't have WSL yet, open **PowerShell as Administrator** and run:
   ```powershell
   wsl --install
   ```
   Restart your PC when asked, then open **Ubuntu** from the Start menu.
2. In the Ubuntu terminal:
   ```bash
   sudo apt update
   sudo apt install -y php-cli php-mbstring git
   ```
3. Check:
   ```bash
   php -v
   php -m | grep mbstring
   git --version
   ```
</details>

<details>
<summary><b>🍎 macOS</b></summary>

1. Install [Homebrew](https://brew.sh) if you don't have it.
2. Then:
   ```bash
   brew install php git
   ```
3. Check with `php -v` (mbstring is included).
</details>

<details>
<summary><b>🐧 Linux (Ubuntu / Debian)</b></summary>

```bash
sudo apt update
sudo apt install -y php-cli php-mbstring git
```
</details>

---

## 🚀 Setup (5 minutes)

### 1. Get the code

```bash
git clone https://github.com/ey3shuh/fiuu-th-demo.git
cd fiuu-th-demo
```

> On Windows/WSL, run this inside the **Ubuntu terminal**, not PowerShell.

### 2. Create your `.env` file

```bash
cp .env.example .env
```

`.env` holds your settings and secret keys. It is **git-ignored**, so it never gets uploaded.

### 3. Start the website

```bash
php -S localhost:8000
```

Leave this terminal open. You should see:
```
PHP 8.x Development Server (http://localhost:8000) started
```

### 4. Open it

Go to **http://localhost:8000** in your browser.

You should see the Baan Kafe menu with a **"DEMO · MOCK GATEWAY"** bar at the top. ✅

### 5. Try a test order (mock mode)

1. Click **+ Add to cart** on any item
2. Click **🛒 Cart** → **Checkout**
3. Keep the pre-filled details → **Pay securely with Fiuu**
4. Click **Proceed to payment**
5. On the **mock Fiuu page**, click **Pay** (or **Simulate failed payment**)
6. You land back on Baan Kafe with ✓ **Payment successful** and **Verified by Fiuu**

Mock mode uses a **fake** Fiuu page on your laptop, so no real money moves and no keys are needed.

To stop the server, press `Ctrl + C` in the terminal.

---

## 🔴 Live mode (real Fiuu gateway)

1. Get the **Verify Key** and **Secret Key** for the merchant **privately** from the team.
2. Open `.env` (`nano .env`, or `code .env` in VS Code) and set:

   ```env
   FIUU_MODE=live
   FIUU_MERCHANT_ID=mobilexdkth_Dev
   FIUU_VERIFY_KEY=paste_verify_key_here
   FIUU_SECRET_KEY=paste_secret_key_here
   FIUU_PAYMENT_URL=https://pay.fiuu.co.th/RMS/pay/
   FIUU_HASH=md5
   ```

3. Save, then restart the server (`Ctrl + C`, then `php -S localhost:8000`).
4. The top bar now shows **"DEMO · LIVE — pay.fiuu.co.th"**.

> ⚠️ **Live mode can charge real money.** Every menu item costs **฿1.00** for safe testing. Buy one item at a time.

### `.env` settings explained

| Setting | What it is | Example |
|---|---|---|
| `FIUU_MODE` | `mock` = fake Fiuu page, `live` = real gateway | `mock` |
| `FIUU_MERCHANT_ID` | The merchant (shop) account on Fiuu | `mobilexdkth_Dev` |
| `FIUU_VERIFY_KEY` | Secret used to create the `vcode` (seals the order) | 32 characters |
| `FIUU_SECRET_KEY` | Secret used to check the `skey` (proves the result came from Fiuu) | 32 characters |
| `FIUU_PAYMENT_URL` | Base URL of the payment page, **ending in `/pay/`**. Don't add the merchant ID or `?amount=…`. | `https://pay.fiuu.co.th/RMS/pay/` |
| `FIUU_HASH` | Must match the merchant's **Hash Algorithm Setting** in the Fiuu Admin Portal | `md5`, `sha256`… |

🔑 All values must belong to the **same merchant on the same server**, otherwise Fiuu rejects the payment.

---

## 🔄 How it works

| Step | File | What happens |
|---|---|---|
| 1 | `index.php` | Menu. Customer adds items to the cart. |
| 2 | `cart.php` | Change quantities. Shows subtotal + 7% VAT (included in prices). |
| 3 | `checkout.php` | Customer enters name, email and mobile number. |
| 4 | `pay.php` | **Server** totals the cart, creates the `vcode` and sends the customer to Fiuu. |
| 5 | Fiuu (or `mock_gateway.php`) | Customer pays. |
| 6 | `return.php` | Fiuu sends the customer back. The server checks the `skey` before showing the result. |

### The two security codes

| Code | Made by | Formula | Purpose |
|---|---|---|---|
| `vcode` | Shop → Fiuu | `hash(amount + merchantID + orderid + verify_key)` | Stops anyone changing the amount on the way to Fiuu |
| `skey` | Fiuu → Shop | `md5(paydate + merchantID + key0 + appcode + secret_key)` | Proves the payment result really came from Fiuu |

The amount always comes from `products.php` on the server, never from the browser.

---

## 📁 Project structure

```
fiuu-th-demo/
├── .env.example       ← copy to .env and fill in
├── config.php         ← loads .env, vcode/skey functions
├── lang.php           ← all English + Thai text
├── products.php       ← menu items and prices
├── cart_lib.php       ← cart helpers
├── index.php          ← menu page
├── cart.php           ← cart page
├── checkout.php       ← customer details
├── pay.php            ← sends the customer to Fiuu
├── return.php         ← payment result
├── mock_gateway.php   ← fake Fiuu page (mock mode only)
├── style.css          ← design
└── partials/          ← header, footer, totals
```

### Common changes

| I want to… | Edit |
|---|---|
| Change any English or Thai text | `lang.php` |
| Add or remove menu items, change prices | `products.php` (prices are in **satang**: `100` = ฿1.00) |
| Change colours or fonts | `style.css` (colours are at the top) |

---

## 🛠️ Troubleshooting

| Problem | Cause | Fix |
|---|---|---|
| `php: command not found` | PHP not installed | See [Prerequisites](#-prerequisites) |
| `Call to undefined function mb_strimwidth()` | mbstring missing | `sudo apt install php-mbstring`, then restart the server |
| `Failed to listen on localhost:8000` | Port already in use | Use another port: `php -S localhost:8080`, or stop the other server |
| **"Not Found"** on every page | Server started from the wrong folder | `cd` into the project first, or run `php -S localhost:8000 -t /path/to/fiuu-th-demo` |
| Page has no colours or layout | Opened a wrong URL like `/something/` | Go to http://localhost:8000 |
| **"Config error: … is missing"** | A value is empty in `.env` while in live mode | Fill it in, save, restart |
| **"FIUU_PAYMENT_URL must be the base URL only"** | URL includes the merchant ID or `?amount=` | Use exactly `https://pay.fiuu.co.th/RMS/pay/` |
| Fiuu shows **HTTP 401** | Merchant ID doesn't exist on that gateway | Check `FIUU_MERCHANT_ID` and `FIUU_PAYMENT_URL` belong together |
| Fiuu shows **HTTP 500** | Problem on the Fiuu server | Ask the team to check the gateway logs |
| Fiuu says **vcode unmatched** | Wrong Verify Key, or `FIUU_HASH` doesn't match the merchant's setting | Re-copy the key, confirm the hash type |
| Result page says **could not be verified** | Wrong Secret Key | Re-copy the Secret Key |
| Changed `.env` but nothing happens | File not saved | Save (`Ctrl + S`), then restart the server |
| "Back to Merchant Site" on Fiuu doesn't return to Baan Kafe | The merchant's Return URL is set elsewhere | Ask the merchant owner to set the Return URL to `http://localhost:8000/return.php` in the Merchant Portal |

---

## 🔒 Security rules

- ❌ **Never commit `.env`** (it's already in `.gitignore`)
- ❌ **Never share keys** in group chats, screenshots or issues. Send them privately.
- ✅ Use **mock mode** for practice, and **live mode** only for real tests
- ✅ This is a **demo**, not production code. Don't reuse it for a real shop without a security review.

---

*Demo merchant for Fiuu Payment Gateway — not a real business.*
