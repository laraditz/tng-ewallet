# Refund

`Tng::refund()->...` — refund a payment and check the refund's status. See the [API Reference table](../README.md#api-reference) in the main README for the full endpoint list.

> **A `U` (Unknown) refund result must never be treated as failed and retried offline** — the original refund may still be processing on TNG's side. Use `inquiry()` below to check the real status. See the README's [Handling U and A results](../README.md#handling-u-unknown-and-a-accepted-results) section.

## create()

`POST /v1/payments/refund`

Refunds all or part of a previously successful payment.

**Params**

| Key | Description |
| --- | --- |
| `refundRequestId` | Your own unique ID for this refund — also used as the local lookup key for `inquiry()` |
| `paymentId` and/or `paymentRequestId` | Identifies the original payment being refunded |
| `refundAmount` | `['currency' => 'MYR', 'value' => '10000']` — smallest currency unit |
| `refundReason` | Free-text reason, shown in TNG's records |

`partnerId` is auto-injected from config.

**Example**

```php
use Laraditz\TngEwallet\Facades\Tng;
use Illuminate\Support\Str;

$refund = Tng::refund()->create([
    'refundRequestId' => (string) Str::uuid(),
    'paymentRequestId' => $paymentRequestId,
    'refundAmount' => ['currency' => 'MYR', 'value' => '10000'],
    'refundReason' => 'Customer requested cancellation',
]);

if ($refund->isUnknown()) {
    // Do not retry or assume failure — inquire later instead.
}
```

**Response (`RefundResponse`)**

| Property | Description |
| --- | --- |
| `refundId` | TNG's identifier for the refund |
| `refundTime` | Timestamp the refund was created |

**Side effect:** creates a `Refund` row (`tng_ewallet_refunds`) with an initial `refund_status` of `PROCESSING`, storing the refund amount, reason, and linked payment identifiers.

## inquiry()

`POST /v1/payments/inquiryRefund`

Checks the real status of a refund.

**Params**

| Key | Description |
| --- | --- |
| `refundRequestId` | The ID you originally passed to `create()` — also used to find the local `Refund` row to update |

`partnerId` is auto-injected from config.

**Example**

```php
$status = Tng::refund()->inquiry([
    'refundRequestId' => $refundRequestId,
]);

$status->refundStatus; // PROCESSING | SUCCESS | FAIL
```

**Response (`InquiryRefundResponse`)**

| Property | Description |
| --- | --- |
| `refundId` | TNG's identifier for the refund |
| `refundRequestId` | Echoes the ID you queried with |
| `refundAmount` | `['currency' => ..., 'value' => ...]` |
| `refundReason` | The reason originally submitted |
| `refundTime` | Timestamp the refund was created |
| `refundStatus` | One of `PROCESSING`, `SUCCESS`, `FAIL` (`Enums\RefundStatus`) |
| `refundFailReason` | Populated when the refund failed |

**Side effect:** updates the matching local `Refund` row's `refund_status`, `result_status`, `result_code`, `refund_time`, and `refund_fail_reason`.
