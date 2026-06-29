# Creative Store

**A bilingual (Arabic / English) e-commerce platform built with Laravel 12.**

Creative Store covers the full online-retail lifecycle: catalogue with product variants, cart and coupons, checkout with online payment through Moyasar, shipping across Saudi zones, order tracking, cancellations, returns and refunds, all managed from a role-based admin dashboard.

> **بالعربي:** متجر إلكتروني ثنائي اللغة (عربي / إنجليزي) مبني بـ Laravel 12. يغطي رحلة الشراء كاملة: منتجات بمقاسات وألوان، سلة وكوبونات، دفع أونلاين عبر ميسر، شحن داخل المملكة، تتبع الطلبات، الإلغاء والمرتجعات والاسترداد، ولوحة تحكم بصلاحيات تفصيلية لكل موظف.

---

## Table of contents

1. [Features](#features)
2. [Tech stack](#tech-stack)
3. [Architecture](#architecture)
4. [Database design](#database-design)
5. [How an order flows](#how-an-order-flows)
6. [Concurrency: what happens when two customers act at once](#concurrency-what-happens-when-two-customers-act-at-once)
7. [Scaling to millions of rows](#scaling-to-millions-of-rows)
8. [Security](#security)
9. [Frontend and design system](#frontend-and-design-system)
10. [Background jobs and scheduler](#background-jobs-and-scheduler)
11. [Installation](#installation)
12. [Configuration](#configuration)
13. [Testing](#testing)
14. [Deployment checklist](#deployment-checklist)
15. [Roadmap](#roadmap)
16. [Changelog of the hardening pass](#changelog-of-the-hardening-pass)

---

## Features

### Storefront
- Arabic and English with localized URLs (`/ar/...`, `/en/...`) and full right-to-left layout
- Categories, attributes and product variants with their own SKU, price, stock and images
- Search, category, price, colour and tag filters with sorting
- Session cart with cart-level and product-level coupons and 15% VAT
- Wishlist, product reviews, blog with comments, CMS pages (home, about, contact)
- Registration with email OTP verification, Google sign-in, password reset
- Address book, zone-based shipping rates and delivery estimates
- Online card payment through Moyasar, verified on the server
- Order history, PDF invoices, self-service cancellation and public shipment tracking
- Abandoned-cart recovery emails

### Admin dashboard
- Three staff roles (owner, admin, employee) plus per-user permission overrides
- Products, variants, images, categories, attributes, coupons
- Orders, payments, refunds, returns, shipments, tracking logs and delivery attempts
- Shipping methods, zones, cities and rates
- Users, roles, permissions, audit log, error log, contact inbox, blog and pages
- Sales statistics and scheduled database backups

---

## Tech stack

| Area | Technology |
| --- | --- |
| Backend | PHP 8.2+, Laravel 12 |
| Database | MySQL 8 / MariaDB 10.6+ |
| Payments | Moyasar |
| Realtime | Laravel Reverb (order events) |
| Queue / cache | Database queue, file or Redis cache |
| Translations | `astrotomic/laravel-translatable`, `mcamara/laravel-localization` |
| PDF | `barryvdh/laravel-dompdf` |
| Auth extras | Laravel Socialite (Google), phone validation |
| Frontend | Blade, Bootstrap 4, jQuery, design tokens in plain CSS |
| Dashboard UI | AdminLTE 3 |

---

## Architecture

```
app/
├── Console/Commands     scheduled jobs (order expiry, abandoned carts, backups)
├── Exceptions           domain exceptions with customer-safe messages
├── Http/
│   ├── Controllers      thin: validate → call a service → respond
│   ├── Middleware       role and permission checks
│   └── Requests         Form Request validation per action
├── Jobs / Mail / Notifications
├── Models               Eloquent models, relationships, scopes
├── Services/
│   ├── Catalog          storefront product queries
│   ├── Checkout         order placement
│   ├── Content          cached CMS pages
│   ├── Coupons          atomic coupon redemption and release
│   ├── Dashboard        stats, cached sidebar badges, variant generator
│   ├── Payments         Moyasar gateway, order payment, refunds
│   └── Shipping         rate selection by zone, city and weight
├── Support              framework-independent helpers (HTML sanitizer, sidebar builder)
└── Traits               Auditable
```

**Principles applied**
- **Single responsibility:** each service owns one business capability. Controllers hold no business rules.
- **Dependency injection:** services are constructor-injected, so they can be swapped or mocked in tests.
- **Open/closed:** the payment flow speaks to a `MoyasarGateway` and an immutable `MoyasarPayment` value object; adding another gateway means adding a gateway class, not editing checkout.
- **Value objects:** gateway payloads are parsed once into a typed, read-only object instead of being read with string keys all over the code.
- **Fail with intent:** `CheckoutException` and `PaymentGatewayException` carry messages that are safe to show customers; anything else is logged and hidden.

---

## Database design

64 tables, grouped by domain:

| Domain | Tables |
| --- | --- |
| Identity & access | `users`, `roles`, `permissions`, `role_user`, `permission_role`, `user_permissions` |
| Catalogue | `categories` (+translations), `products` (+translations, images), `attributes`, `attribute_values`, `product_variants`, `product_variant_values` |
| Shopping | `carts`, `cart_items`, `wishlists`, `wishlist_items`, `reviews`, `coupons`, `coupon_usages` |
| Orders | `orders`, `order_items`, `order_address_snapshots`, `order_status_logs` |
| Payments | `payments`, `payment_webhook_logs`, `refunds`, `returns`, `return_items` |
| Shipping | `shipping_methods`, `shipping_zones`, `shipping_zone_cities`, `shipping_rates`, `shipments`, `shipment_tracking_logs`, `delivery_attempts` |
| Inventory | `inventory_movements`, `inventory_reservations` |
| Content | `pages` (+translations), `blogs`, `blog_categories`, `blog_comments`, `contact_messages` |
| Operations | `audit_logs`, `error_logs`, `notification_logs`, `system_backups`, `abandoned_cart_recoveries`, `settings` |

**Design decisions**
- **Snapshots, not references, for history.** An order stores the address, product name, SKU and unit price it was bought at (`order_address_snapshots`, `order_items.*_snapshot`). Editing a product or an address never rewrites past invoices.
- **Money** is `DECIMAL(12,2)`. Floats are never used for amounts; gateway amounts are compared in halalas (integers).
- **Uniqueness lives in the database:** order numbers, SKUs, slugs, coupon codes, one review per user per product, one wishlist per user, one usage row per coupon per order, one payment per gateway transaction.
- **CHECK constraints** stop invalid data at the database level: stock and quantities can't go negative, ratings stay 1 to 5, money columns on orders, payments, refunds and shipping rates can't be negative, and percentage coupons can't exceed 100.
- **Foreign keys everywhere** with explicit `restrict`, `cascade` or `null on delete` per relationship. For example, a product with orders cannot be deleted.
- **Audit trail:** `order_status_logs` records every status transition; `audit_logs` records who changed which model and what changed.

---

## How an order flows

```
Cart ──► Shipping (address + rate) ──► Place order ──► Moyasar payment page
                                          │                    │
                                          │          callback (browser) ─┐
                                          │          webhook (server)  ──┤
                                          ▼                              ▼
                                 order: pending/pending        OrderPaymentService
                                 stock reserved                verify amount + currency
                                 coupon use recorded           lock order row, confirm once
                                          │                              │
                       not paid in 60 min │                              ▼
                                          ▼                     order: confirmed/paid
                          orders:expire-unpaid                  notify customer + staff
                          cancel, restock, release coupon
```

1. **Place order** (`CheckoutService`): inside one transaction, reload every product and variant from the database, lock the variant rows, compare the live subtotal with what the customer saw, decrement stock, redeem coupons, snapshot the address, write items, a status log and a pending payment.
2. **Pay:** the customer pays on the Moyasar form. Moyasar redirects back (callback) and also calls the webhook.
3. **Verify** (`OrderPaymentService`): fetch the payment from Moyasar's API (never trust the redirect), check amount and currency against the order, then confirm. Both paths call the same service.
4. **Expire:** unpaid orders older than 60 minutes are cancelled every 15 minutes and their stock and coupon uses are released.

---

## Concurrency: what happens when two customers act at once

Every place where two requests can race has an explicit guard:

| Scenario | Guard |
| --- | --- |
| Two customers buy the last unit | Variant rows are locked with `SELECT … FOR UPDATE` (in id order, so no deadlocks). The second checkout waits, sees stock is 0 and gets "Not enough stock". |
| Two customers redeem the last coupon use | Coupon rows are locked before `used_count` and `per_user_limit` are checked. Exactly one succeeds. |
| Customer double-clicks "Place order" | A per-customer cache lock serializes checkout, and the cart is cleared once the order exists, so the second click cannot create a duplicate order. |
| Callback and webhook arrive together | The order row is locked; whichever runs second sees `payment_status = paid` and does nothing. Emails are sent once. |
| Payment lands after the order expired | The payment is recorded and logged as needing a refund; the cancelled order is not reopened. |
| Customer or admin clicks "Cancel" twice | The order is re-read under lock; the second request sees `cancelled` and stops, so stock is restored once. |
| Wishlist heart clicked twice quickly | The customer's wishlist row is locked; the toggle runs once. |
| Same review submitted twice | The `(product_id, user_id)` unique index rejects the second; the user gets a friendly message. |
| Moyasar redelivers the same webhook | The `(gateway, event_id)` unique index plus `createOrFirst` makes redelivery idempotent: processed events are acknowledged without running again. |
| Burst of identical errors | An atomic `Cache::add` key lets one log row through per error per two minutes. |

---

## Scaling to millions of rows

The goal is that request time depends on the page size, not on how big the tables are.

**Queries**
- Every list is paginated or limited. The home slider takes 16 products, product pages load the latest 20 reviews, and review count and average come from one aggregate query.
- N+1 queries removed from the storefront: wishlist state for a whole product grid is loaded with one query.
- Background work walks tables with `chunkById` and caps each run (abandoned carts: 1,000 per run, recent 7-day window only).

**Indexes** added for the queries that run on every request:

| Table | Index | Used by |
| --- | --- | --- |
| `products` | `(status, created_at)`, `(status, base_price)` | storefront listing and sorting |
| `blogs` | `(status, published_at)` | blog feed |
| `orders` | `(status, payment_status, placed_at)`, `(status, id)`, `(payment_status)` | order expiry, dashboard badges |
| `payments`, `shipments`, `abandoned_cart_recoveries` | `(status)` | dashboard badges |
| `contact_messages` | `(is_read, id)` | unread inbox |
| `reviews` | `(is_approved, id)` | moderation queue |
| `carts` | `(updated_at)` | abandoned-cart scan |
| `coupon_usages` | `(coupon_id, user_id)` | per-user coupon limit |
| `audit_logs` | `(auditable_type, auditable_id)`, `(created_at)` | audit lookups, pruning |
| `shipments` | `(tracking_number)` | public order tracking |
| `product_translations` | `FULLTEXT(name)` | product search |

**Caching**
- CMS pages: 30 minutes, invalidated when a page is saved.
- Dashboard statistics: 5 minutes.
- Sidebar and navbar badges: computed at most once a minute with stale-while-revalidate (`Cache::flexible`), replacing about 40 `COUNT` queries per dashboard page.

**Data growth**
- Audit logs are kept 365 days, error logs and processed webhook logs 180 days, pruned nightly by `model:prune`.
- Audit entries store only changed columns.

**When traffic grows further**
- Switch `CACHE_STORE`, `SESSION_DRIVER` and `QUEUE_CONNECTION` to `redis` so several app servers share state.
- Route read-heavy listing queries to a MySQL read replica (`DB_READ_HOST`).
- Product search already uses a MySQL FULLTEXT index; move to Meilisearch or OpenSearch for typo tolerance and facets.
- Use cursor pagination on very deep dashboard lists.
- Serve `storage` and `public/css` through a CDN (S3 support is already wired in `MoveImagesToS3`).

---

## Security

- **Payments:** amounts are verified with Moyasar's API on the server; the webhook secret is compared in constant time; the webhook is CSRF-exempt and rate-limited.
- **Rate limiting:** login and registration (5/min per email+IP), OTP (5 tries per 10 min, then the code is destroyed), password reset, contact, comments, coupons, order tracking, checkout and webhooks.
- **Authentication:** session regenerated on login and invalidated on logout; disabled accounts cannot sign in; OTPs compared with `hash_equals`.
- **Authorization:** every dashboard route has a `permission:` middleware; customers can only reach their own orders, addresses and invoices.
- **XSS:** Blade escapes by default; rich blog content goes through an allow-list HTML sanitizer that strips scripts, event handlers, inline styles and `javascript:` links.
- **SQL injection:** only Eloquent and parameter bindings; `LIKE` wildcards are escaped in the home page search.
- **Uploads:** images only (`jpg`, `jpeg`, `png`, `webp`) with size limits.
- **Secrets:** never committed; password hashes, tokens, OTPs and gateway payloads are redacted from audit logs; errors shown to customers never include exception details.

---

## Frontend and design system

- **Design tokens** (`public/css/site/tokens.css`) define colour, type scale, spacing, radius, shadows and motion in one place. All storefront stylesheets read from them.
- **Base layer** (`base.css`): visible keyboard focus, `prefers-reduced-motion`, 44px touch targets, no horizontal scroll on phones, Arabic font stack for RTL.
- **Stylesheets instead of inline styles:** about 9,000 lines of CSS moved out of Blade templates into 32 cacheable files, versioned by modification time (`versioned_asset()`).
- **Consistent breakpoints** aligned with the Bootstrap grid (576 / 768 / 992 / 1200).
- **Performance:** below-the-fold images are lazy-loaded with async decoding; product images reserve their space to avoid layout shift.
- **Accessibility:** every page declares its language and text direction; logos have meaningful alt text.

---

## Background jobs and scheduler

| Command | Schedule | Purpose |
| --- | --- | --- |
| `orders:expire-unpaid` | every 15 min | cancel unpaid orders, release stock and coupons |
| `carts:process-abandoned` | every 5 min | send recovery emails in bounded batches |
| `backup:run scheduled` | every 12 h | database backup with retention |
| `model:prune` | daily 03:30 | trim audit, error and webhook logs |

Run the worker and scheduler in production:

```bash
php artisan queue:work --tries=3
php artisan schedule:work      # or a cron entry calling schedule:run every minute
```

---

## Installation

Requirements: PHP 8.2+ (`intl`, `gd`, `mbstring`, `pdo_mysql`), Composer 2, MySQL 8, Node.js 20+.

```bash
git clone https://github.com/m77mdabdo/creative-store.git
cd creative-store

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate

# set DB_*, MAIL_*, MOYASAR_* and GOOGLE_* in .env
php artisan migrate --seed
php artisan storage:link

php artisan serve
```

The seeders create roles, permissions, Saudi shipping zones and rates.

Load the full demo catalogue (90 products in 9 categories, Arabic and English, colour/size variants with stock and real product photography):

```bash
php artisan db:seed --class=DemoCatalogSeeder
```

Photos are from [Unsplash](https://unsplash.com/license) (free for commercial use). The seeder downloads them once to the media disk, can be re-run safely and archives the old placeholder products.

---

## Configuration

| Key | Purpose |
| --- | --- |
| `MOYASAR_PUBLISHABLE_KEY`, `MOYASAR_SECRET_KEY` | payment form and server verification |
| `MOYASAR_WEBHOOK_SECRET` | must match the secret configured in Moyasar |
| `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` | Google sign-in |
| `BACKUP_RETENTION_DAYS` | how long backups are kept (default 30) |
| `REVERB_*` | realtime broadcasting |

Register the webhook in the Moyasar dashboard:

```
https://your-domain.com/webhooks/moyasar
```

---

## Testing

```bash
php artisan test
```

Unit tests cover the HTML sanitizer (script, event handler, `javascript:` and protocol-relative URL removal, Arabic text preserved) and Moyasar payment handling (payload parsing, status mapping, webhook secret check, payment id validation).

---

## Deployment checklist

```bash
APP_ENV=production
APP_DEBUG=false

composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

- Serve over HTTPS only.
- Run `queue:work` under Supervisor and the scheduler from cron.
- Point the Moyasar webhook at the production URL.

---

## Roadmap

- Feature tests for checkout and payment against a MySQL test database
- Redis for cache, sessions and queue
- Move the remaining dashboard controllers onto services the way checkout and payments are
- Build storefront CSS through Vite for minification

---

## Changelog of the hardening pass

**Payments**
- Split Moyasar handling into a gateway, a payment value object and an idempotent order payment service.
- Removed a debug dump that printed the gateway response to customers.
- The webhook was rejected by CSRF on every call; it is now exempt and rate-limited.
- Callback and webhook could both confirm an order and send emails twice; they now share one locked code path.
- Webhook payments are now checked against the order amount.
- Gateway statuses such as `voided` and `initiated` no longer break the `payments.status` enum.

**Checkout and orders**
- Order placement moved to `CheckoutService` with live re-pricing and stock locking.
- Orders could not be created because `payment_status = pending` was missing from the enum.
- Coupon limits were never enforced (`used_count` never increased); redemption is now atomic.
- Double-submit protection on checkout.
- Unpaid orders expire and release stock and coupons.
- Customer and dashboard cancellations are safe against double clicks; dashboard cancellation logged the wrong previous status.

**Security**
- Rate limiting across authentication, forms, coupons, checkout and webhooks.
- OTP attempts capped; constant-time comparison.
- Disabled accounts blocked at login; session invalidated on logout.
- Blog HTML sanitized before rendering.
- Secrets removed from audit logs.

**Performance and data**
- Home page: bounded product query, single wishlist query, no per-request schema lookups.
- Draft blog posts were shown on the home page because string statuses were compared with a boolean column.
- New indexes for storefront, dashboard, coupons and audit logs.
- Dashboard badges cached instead of about 40 count queries per page.
- Abandoned-cart job rewritten to run in bounded batches.
- Product pages load the latest reviews and aggregate totals in SQL.
- Log tables pruned automatically.
- `app/helpers.php` was never autoloaded, so the error log stayed empty; it is now registered in Composer.
- Backup settings read from config so they survive `config:cache`.

**Frontend**
- Design tokens and base styles.
- Inline CSS extracted into cached, versioned stylesheets.
- Unified breakpoints, lazy-loaded images, language and direction on every page.

### Second pass

**Database**
- Full schema review; added CHECK constraints on money columns and coupon values (existing bad rows are reported, not silently rejected).
- FULLTEXT index on product names, used by both storefront searches with an escaped `LIKE` fallback for short terms.
- Index for public shipment tracking.
- Redelivered webhooks no longer fail on the unique event id.

**Media**
- Dashboard uploads were saved on the private disk and never appeared on the site; all media now goes through one public media disk (`media_disk()` / `media_url()`).
- Image URLs follow the current host instead of `APP_URL`, so they work on any port or domain.

**Catalogue**
- `DemoCatalogSeeder`: 90 bilingual products, variants with stock, real photography.

**Cart**
- Archived products can't be added; products sold in variants require a chosen variant.

**Frontend**
- Product cards on phones showed no product name (the wishlist icon took the full width).
- Hero slides got a scrim and light text for readable headlines.
- Missing Arabic messages for the map picker and order statuses added.

---

## License

MIT
