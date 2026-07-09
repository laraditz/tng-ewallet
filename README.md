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
