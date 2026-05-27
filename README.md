# Creative Store

A bilingual (Arabic / English, full RTL) e-commerce platform built with Laravel 12. It covers the whole storefront journey, from catalogue and variants to checkout, online payment, shipping and returns, and ships with a role-based admin dashboard for running the store day to day.

## Features

**Storefront**
- Arabic and English with localized URLs and right-to-left layouts
- Product catalogue with categories, attributes and variants (colour, size, SKU-level stock and price)
- Session cart with product and cart-level coupons, VAT and live stock checks
- Wishlist, product reviews, blog with comments, CMS pages
- Customer accounts with email OTP verification and Google sign-in
- Address book, zone-based shipping rates and delivery estimates
- Online payment through Moyasar with server-side verification and signed webhooks
- Order history, PDF invoices, cancellations, returns and public shipment tracking
- Abandoned-cart recovery emails

**Dashboard**
- Owner / admin / employee roles with granular permissions
- Products, variants, images, categories, attributes, coupons
- Orders, payments, refunds, returns, shipments and delivery attempts
- Shipping methods, zones and rates
- Users, roles, audit log, error log, contact inbox
- Sales statistics and database backups

## Architecture

| Layer | Responsibility |
| --- | --- |
| `app/Http/Controllers` | Thin controllers: validate, delegate, respond |
| `app/Http/Requests` | Form Request validation and authorization |
| `app/Services` | Business logic (`Checkout`, `Payments`, `Catalog`, `Shipping`, `Content`, `Dashboard`) |
| `app/Models` | Eloquent models, relationships and query scopes |
| `app/Support` | Framework-independent helpers such as the HTML sanitizer |
| `public/css/site` | Storefront stylesheets built on shared design tokens |

Key design decisions:
- **Checkout** re-prices the cart from the catalogue and locks variant stock inside a single transaction.
- **Payments** are verified against Moyasar's API; callback and webhook paths share one idempotent service guarded by a row lock, so an order is never confirmed twice.
- **Unpaid orders** expire automatically and their stock is released.
- **Rich text** from the dashboard is passed through an allow-list sanitizer before rendering.
- **Rate limiting** protects login, registration, OTP, coupons, forms, checkout and webhooks.

## Requirements

- PHP 8.2+ with `intl`, `gd`, `mbstring`, `pdo_mysql`
- Composer 2
- MySQL 8 / MariaDB 10.6+
- Node.js 20+

## Getting started

```bash
git clone https://github.com/m77mdabdo/creative-store.git
cd creative-store

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate

# configure DB_*, MAIL_*, MOYASAR_* and GOOGLE_* in .env
php artisan migrate --seed
php artisan storage:link

php artisan serve
```

Run the queue worker and scheduler for emails, cart recovery, backups and order expiry:

```bash
php artisan queue:work
php artisan schedule:work
```

## Payments

Set your Moyasar keys in `.env` and register the webhook URL in the Moyasar dashboard:

```
https://your-domain.com/webhooks/moyasar
```

Use the same value for `MOYASAR_WEBHOOK_SECRET` in both places.

## Tests

```bash
php artisan test
```

## License

MIT
