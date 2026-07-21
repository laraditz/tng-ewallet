# Changelog

All notable changes to `laraditz/tng-ewallet` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-07-22

### Added
- Cashier Payment return page — `pay()` now defaults `paymentReturnUrl` to this package's own `tng-ewallet.return` route (`config('tng-ewallet.return_path')`, default `/tng-ewallet/return`). The route looks up the matching `Payment`, calls `inquiry()` live, and renders one of three states: not-found, status (with amount/currency, reference, and fail reason if any), or a generic inquiry-failed state. See `docs/payment.md#return-page`.
- `customerReturnUrl` — an optional `pay()` parameter for your own "send the customer back here" destination. Package-only: stripped from the outbound TNG request, persisted on the `Payment` row, and used as the return page's "Back" link (falling back to `config('tng-ewallet.default_return_url')`).
- `TNG_RETURN_PATH` and `TNG_DEFAULT_RETURN_URL` config keys.
- `tng_ewallet_payments.customer_return_url` column — existing installs need `php artisan vendor:publish --tag=tng-ewallet-migrations && php artisan migrate`.
- The return page view, publishable under the `tng-ewallet-views` tag.

## [1.0.2] - 2026-07-21

### Added
- README: an "API Reference" section listing every wrapped endpoint (resource, method, TNG path, description) with an example for each, plus links to new per-resource docs (`docs/authorization.md`, `docs/payment.md`, `docs/refund.md`, `docs/user-and-messaging.md`) covering full parameter lists, response DTO fields, and local persistence side effects.

## [1.0.1] - 2026-07-13

### Added
- `pay()` now defaults `envInfo` to `['terminalType' => 'MINI_APP']` when the caller doesn't supply one; any caller-supplied keys still take precedence.
- README: RSA key generation instructions (your keypair vs. TNG's — two separate keypairs, easy to mix up), and encryption key generation moved into Installation so neither is missed.

### Changed
- **Minimum supported Laravel version raised to 11.0** (previously 10.0) — avoids a `doctrine/dbal` dependency the `redirection_url` migration below would otherwise require on Laravel 10.

### Fixed
- Outbound requests now include the required `/acl/api` path segment (e.g. `https://api-sd.tngdigital.com.my/acl/api/v1/payments/pay`) — every API call was being sent to the wrong path. Request signing, response-signature verification, and the `tng_ewallet_api_logs.endpoint` field are all updated to match the corrected path.
- `tng_ewallet_payments.redirection_url` widened from `varchar(255)` to `text` — TNG's real Cashier Payment redirect URL (with its RSA-signed query string) regularly exceeds 255 characters and was truncating on insert. Existing installs need `php artisan vendor:publish --tag=tng-ewallet-migrations && php artisan migrate` to pick up the new migration.

## [1.0.0] - 2026-07-10

### Added
- RSA256 request signing and response signature verification for every TNG Mini Program OpenAPI call.
- Cashier Payment flow — `Tng::payment()->pay()` and `->inquiry()`.
- Agreement Payment (stored-credential) flow — `Tng::authorization()->prepare()`, `->applyToken()`, `->cancelToken()`.
- Refunds — `Tng::refund()->create()` and `->inquiry()`.
- User info lookup and messaging by access token — `Tng::user()->inquiryByAccessToken()`, `Tng::message()->sendByAccessToken()`.
- Inbound `notifyPayment` webhook handling: signature-verified middleware, the mandatory ack response sent before processing, and a `PaymentNotified` event for your own listeners.
- A full persistence layer: every outbound API call, access token, payment, refund, and inbound notification is recorded in the package's own database tables.
- Publishable config and migrations via `vendor:publish`, with duplicate-migration detection so re-publishing on upgrade never creates duplicate files.
- A dedicated encryption key (`TNG_ENCRYPTION_KEY`) for sensitive stored data, independent of the host application's `APP_KEY`.
- `Tng` facade, service provider, and support for Laravel 10 through 13 on PHP 8.1+.
