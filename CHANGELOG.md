# Changelog

All notable changes to `laraditz/tng-ewallet` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
