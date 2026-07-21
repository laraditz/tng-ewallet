# Payment

`Tng::payment()->...` — create a payment (Cashier or Agreement) and check its status. See the [README's Cashier Payment section](../README.md#cashier-payment-redirect-checkout) for the end-to-end redirect + webhook flow, and the [API Reference table](../README.md#api-reference) for the full endpoint list.

## pay()

`POST /v1/payments/pay`

Creates a payment. Works for both the Cashier Payment (hosted redirect) and Agreement Payment (stored access token) flows — which one TNG runs depends on `paymentFactor` and whether `paymentAuthCode` is present.

**Params**

| Key | Description |
| --- | --- |
| `paymentRequestId` | Your own unique ID for this payment — the SDK never generates one for you |
| `paymentOrderTitle` | Order description shown to the user |
| `productCode` | TNG product code — differs between Cashier Payment and Agreement Payment |
| `paymentAmount` | `['currency' => 'MYR', 'value' => '10000']` — `value` is the smallest currency unit |
| `paymentFactor` | `['isCashierPayment' => true]` or `['isAgreementPay' => true]` |
| `paymentAuthCode` | Required for Agreement Payment — the access token to charge |
| `customerReturnUrl` | Optional. Your own "send the customer back here" destination — see [Return page](#return-page) below. Package-only: never sent to TNG, stripped from the outbound request. |

Auto-filled if omitted — you don't need to pass these unless overriding:

| Key | Default |
| --- | --- |
| `partnerId` | From config |
| `paymentNotifyUrl` | This package's own webhook route (`config('tng-ewallet.notify_path')`) |
| `paymentReturnUrl` | This package's own return route (`config('tng-ewallet.return_path')`), with `paymentRequestId` appended — see [Return page](#return-page) below |
| `envInfo` | Merged with `['terminalType' => 'MINI_APP']` |

> Don't override `paymentNotifyUrl` or `paymentReturnUrl` unless you're prepared to handle them yourself — see the note in the main [README](../README.md#cashier-payment-redirect-checkout).

**Example**

```php
use Laraditz\TngEwallet\Facades\Tng;
use Illuminate\Support\Str;

$response = Tng::payment()->pay([
    'paymentRequestId' => (string) Str::uuid(),
    'paymentOrderTitle' => 'Order #1234',
    'productCode' => '51051000101000100001',
    'paymentAmount' => ['currency' => 'MYR', 'value' => '10000'],
    'paymentFactor' => ['isCashierPayment' => true],
]);

if ($response->isAccepted()) {
    return redirect()->away($response->actionForm->redirectionUrl);
}
```

**Response (`PayResponse`)**

| Property | Description |
| --- | --- |
| `paymentId` | TNG's identifier for the payment |
| `paymentTime` | Timestamp the payment was created |
| `actionForm` | Nullable `ActionForm` value object — see below |
| `authExpiryTime` | Expiry of the authorization, where applicable |

`actionForm` (present when `isAccepted()`) exposes: `actionFormType`, `orderCode`, `redirectionUrl`.

**Side effect:** creates a `Payment` row (`tng_ewallet_payments`) mapping `resultStatus` to a `PaymentStatus` enum (`Created`/`Accepted`/`Success`/`Failed`/`Unknown`), storing currency/amount, the action form fields, `customer_return_url` (if you supplied one), and the full raw response.

## Return page

`GET config('tng-ewallet.return_path')` (default `/tng-ewallet/return`), route name `tng-ewallet.return`.

TNG redirects the customer's browser here once the hosted cashier page finishes — the URL is the one `pay()` auto-filled into `paymentReturnUrl` above, with `paymentRequestId` appended. This package owns the whole page: it looks up the matching `Payment`, calls `inquiry()` live, and renders one of three states:

- **Not found** — no `Payment` matches the `payment_request_id` in the URL (missing, unknown, or stale link). Shown as a friendly page, not a bare 404.
- **Status** — the matched payment's live status, amount/currency, payment reference, and date/time, plus the fail reason whenever `inquiry()` returns one.
- **Inquiry failed** — shown whenever `inquiry()` couldn't produce a confirmed answer, whether that's a technical failure (network error, timeout) or a well-formed "TNG has no record of this order" response. A generic "we couldn't confirm this payment's status right now" message is shown either way, rather than guessing.

Every state includes a "Back" link/button, pointing at your `customerReturnUrl` (from the original `pay()` call) if one was supplied, otherwise `config('tng-ewallet.default_return_url')` (which itself defaults to `config('app.url')`).

The `inquiry()` call made here is **read-only** — it's never written back to the `Payment` record. The `notifyPayment` webhook pipeline remains the only writer of persisted payment status, so this page can't race with it.

The view is published under the `tng-ewallet-views` tag if you want to override its look:

```bash
php artisan vendor:publish --tag=tng-ewallet-views
```

## inquiry()

`POST /v1/payments/inquiryPayment`

Checks the real status of a payment — the recommended way to resolve a `U` (Unknown) result from `pay()` rather than assuming success or failure. See the README's [Handling U and A results](../README.md#handling-u-unknown-and-a-accepted-results) section for why this matters.

**Params**

| Key | Description |
| --- | --- |
| `paymentRequestId` | The ID you originally passed to `pay()` |

`partnerId` is auto-injected from config.

**Example**

```php
$status = Tng::payment()->inquiry([
    'paymentRequestId' => $paymentRequestId,
]);

if ($status->isSuccessful()) {
    // Safe to mark the order paid.
} elseif ($status->isFailed()) {
    // $status->paymentFailReason explains why.
}
```

**Response (`InquiryPaymentResponse`)**

| Property | Description |
| --- | --- |
| `paymentId` | TNG's identifier for the payment |
| `paymentRequestId` | Echoes the ID you queried with |
| `paymentAmount` | `['currency' => ..., 'value' => ...]` |
| `paymentTime` | Timestamp the payment was created |
| `paymentStatus` | TNG's payment status string |
| `paymentFailReason` | Populated when the payment failed |
| `authExpiryTime` | Expiry of the authorization, where applicable |

This call has no local persistence side effect — it only reads from TNG.
