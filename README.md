# laraditz/tng-ewallet

A Laravel SDK for Touch 'n Go's Mini Program OpenAPI — RSA256 request signing, response/webhook signature verification, and a persistence layer covering every call, access token, payment, refund, and inbound notification.

## Installation

```bash
composer require laraditz/tng-ewallet
```

Publish the config file:

```bash
php artisan vendor:publish --tag=tng-ewallet-config
```

Migrations auto-load — no publish step is needed for them. Run them the normal way:

```bash
php artisan migrate
```

## Configuration

Set these in your `.env`:

| Variable | Description | Default |
|---|---|---|
| `TNG_SANDBOX` | Use TNG's sandbox host instead of production | `true` |
| `TNG_BASE_URL` | Explicit base URL override — takes precedence over `TNG_SANDBOX` if set | *(none)* |
| `TNG_CLIENT_ID` | Your TNG-assigned Client-Id | *(required)* |
| `TNG_PARTNER_ID` | Your TNG-assigned partner ID | *(required)* |
| `TNG_PRIVATE_KEY_PATH` | Path to your RSA private key (PEM), used to sign every outbound request | `storage_path('tng/private.pem')` |
| `TNG_PUBLIC_KEY_PATH` | Path to TNG's RSA public key (PEM), used to verify responses and inbound notifications | `storage_path('tng/tng_public.pem')` |
| `TNG_KEY_VERSION` | Key version sent in the `Signature` header | `1` |
| `TNG_VERIFY_RESPONSE_SIGNATURE` | Verify every response's signature before returning data | `true` |
| `TNG_TIMEOUT` | HTTP timeout in seconds | `30` |
| `TNG_NOTIFY_PATH` | Path the inbound `notifyPayment` webhook is registered at | `/tng-ewallet/notify` |

`client_id`, `partner_id`, and `private_key_path` are required — a missing value throws `ConfigurationException` before any HTTP call is made.

## Cashier Payment (redirect checkout)

This is the documented golden path: create a payment, redirect the user to TNG's hosted cashier page, then receive the result asynchronously via the webhook.

```php
use Laraditz\TngEwallet\Facades\Tng;

$response = Tng::payment()->pay([
    'partnerId' => config('tng-ewallet.partner_id'),
    'paymentRequestId' => (string) Str::uuid(), // your own unique ID — the SDK never generates one for you
    'paymentOrderTitle' => 'Order #1234',
    'productCode' => '51051000101000100001', // Cashier Payment product code
    'paymentAmount' => ['currency' => 'MYR', 'value' => '10000'], // smallest currency unit
    'paymentFactor' => ['isCashierPayment' => true],
    'paymentNotifyUrl' => route('tng-ewallet.notify'), // or config('tng-ewallet.notify_path') resolved to an absolute URL
    'envInfo' => ['terminalType' => 'MINI_APP'],
]);

if ($response->isAccepted()) {
    // The normal Cashier Payment path — redirect the user to finish payment.
    return redirect()->away($response->actionForm->redirectionUrl);
}

if ($response->isFailed()) {
    // $response->resultCode / $response->resultMessage explain why.
}

if ($response->isUnknown()) {
    // See "Handling U (Unknown) results" below — do not treat as final.
}
```

`paymentNotifyUrl` must point at this package's auto-registered webhook route (`config('tng-ewallet.notify_path')`, default `/tng-ewallet/notify`) — that's what TNG calls when the payment reaches a final state. Listen for the result in your own listener:

```php
use Laraditz\TngEwallet\Events\PaymentNotified;

class HandlePaymentNotified implements ShouldQueue // recommended, though not required — see the afterResponse() note below
{
    public function handle(PaymentNotified $event): void
    {
        $payload = $event->payload; // the raw, already-verified notifyPayment body

        // $payload['paymentResult']['resultStatus'] is 'S' or 'F'.
        // Persist your own order state here — this package never touches
        // your application's domain model, only its own audit tables.
    }
}
```

## Agreement Payment (stored-credential auto-debit)

Used once a user has bound their account to your app and authorized recurring/one-off charges without re-entering payment details each time. Three steps: bind, apply for a token, then pay with it.

```php
use Laraditz\TngEwallet\Facades\Tng;

// 1. Prepare — kicks off the binding flow, returns a URL for the user to authorize.
$prepare = Tng::authorization()->prepare([
    'referenceClientId' => 'your-mini-program-client-id',
]);
// Redirect / hand $prepare->authURL to your Mini Program frontend.

// 2. Apply for an access token — after the user authorizes, you'll receive an authCode.
$token = Tng::authorization()->applyToken([
    'grantType' => 'AUTHORIZATION_CODE',
    'authCode' => $authCodeFromMiniProgram,
]);
// $token->accessToken / $token->customerId are now persisted in tng_ewallet_access_tokens.

// 3. Pay using the access token — no cashier redirect needed.
$response = Tng::payment()->pay([
    'partnerId' => config('tng-ewallet.partner_id'),
    'paymentRequestId' => (string) Str::uuid(),
    'paymentOrderTitle' => 'Order #1234',
    'productCode' => '51051000101000100031', // Agreement Payment product code
    'paymentAmount' => ['currency' => 'MYR', 'value' => '10000'],
    'paymentFactor' => ['isAgreementPay' => true],
    'paymentAuthCode' => $token->accessToken,
    'paymentNotifyUrl' => route('tng-ewallet.notify'),
    'envInfo' => ['terminalType' => 'MINI_APP'],
]);
```

Refreshing an expired access token uses the same `applyToken()` call with `grantType: REFRESH_TOKEN` — each call creates a **new** `tng_ewallet_access_tokens` row rather than overwriting the old one, so rotation history is preserved.

To revoke a binding:

```php
Tng::authorization()->cancelToken(['accessToken' => $token->accessToken]);
```

## User info and messaging

Given an access token from the Agreement Payment flow above:

```php
$userInfo = Tng::user()->inquiryByAccessToken(['accessToken' => $token->accessToken]);
$userInfo->userInfo; // raw array, e.g. ['userId' => '...']

Tng::message()->sendByAccessToken([
    'accessToken' => $token->accessToken,
    'message' => 'Your order has shipped!',
]);
```

## Handling `U` (Unknown) and `A` (Accepted) results

Every response DTO exposes `isSuccessful()` (`S`), `isAccepted()` (`A`), `isFailed()` (`F`), and `isUnknown()` (`U`). Two of these need explicit caller attention beyond a simple if/else:

**`isAccepted()` on `pay()` is not a failure** — it's TNG's normal Cashier Payment success path. `PayResponse::isAccepted()` plus `$response->actionForm->redirectionUrl` is the documented golden-path branch (see the Cashier Payment example above). Treating `A` as an error will break the most common call in this SDK.

**`isUnknown()` must never be treated as final.** Per TNG's own docs, a `U` result means an unknown exception occurred on the wallet's side — the SDK does **not** auto-retry or auto-inquire on your behalf. Specifically for `pay()`:

- A `U` result must never be independently refunded or re-charged offline — the payment may still complete on TNG's side after you've observed `U`.
- Use `Tng::payment()->inquiry(['paymentRequestId' => $id])` to check the real status once you're ready, rather than assuming failure or success.
- The same caution applies to `Tng::refund()->create()` — a `U` refund result must not be treated as failed and retried, since the original refund may still be processing.

## Operational notes

**The webhook ack is guaranteed to be sent before `PaymentNotified` fires**, via Laravel's `dispatch(...)->afterResponse()` — not a queue. This relies on `fastcgi_finish_request()`, which is standard under PHP-FPM (the overwhelming majority of production Laravel deployments). Under non-FPM SAPIs (`php artisan serve`, some Octane configurations), verify this ordering holds in your own environment before relying on it in production.

**There is no retry if your own `PaymentNotified` listener throws**, and none from TNG either — by the time the job runs, TNG has already received a successful ack and will not redeliver. Make listeners resilient (catch your own exceptions, log failures, reconcile via `Tng::payment()->inquiry()` if needed) rather than assuming the event always completes cleanly.

**Listeners must be idempotent.** TNG retries the notification until it receives an `S` ack; each delivery gets its own `tng_ewallet_notifications` row (the package does not de-duplicate), so the same `PaymentNotified` event can fire more than once for the same payment. Design your listener so processing the same payload twice is safe.

**`notify_path` must match exactly** what you configured as `paymentNotifyUrl` when calling `pay()` — including scheme, host, and any trailing slash. A mismatch (e.g. a reverse proxy that rewrites paths) causes legitimate notifications to be rejected with 401, not silently accepted; there's no way for this package to detect a misconfigured path itself.

**Add your own rate limiting** (via your reverse proxy, CDN, or a `throttle:` middleware sized to your expected TNG callback volume) if the notify endpoint needs protection beyond signature verification — the package intentionally ships with none, since a wrong hardcoded limit could reject legitimate TNG traffic.

**Rotate your TNG public key file atomically** (write to a temp file, then `rename()`) rather than editing it in place — the key is read fresh from disk on every verification, and a torn read during a non-atomic rewrite would cause verification failures, not a security gap (verification fails closed either way).

## Security

- `access_token` (in `tng_ewallet_access_tokens`) is stored **as plaintext**, deliberately — `cancelToken()` and `user()` need to look it up by exact value, and Laravel's `encrypted` cast is non-deterministic (a random IV per encryption), which breaks `WHERE access_token = ?` lookups. `refresh_token` has no such lookup requirement anywhere in the package, so it **is** encrypted. Apply database-level access restrictions to this table — it holds bearer credentials that authorize auto-debit charges.
- `tng_ewallet_api_logs.request_payload`/`response_payload` are encrypted at rest, since they capture full request/response bodies (including `accessToken`/`refreshToken` values that pass through `applyToken()`/`cancelToken()`) with no exact-match lookup requirement to justify plaintext.
- Every table in this package is a full audit trail — every call, success or failure, is recorded. Treat these tables as containing sensitive payment and customer data, and apply the same access controls you'd use for any PII/financial-data store.
