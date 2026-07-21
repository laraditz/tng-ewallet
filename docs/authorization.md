# Authorization

`Tng::authorization()->...` — the Agreement Payment binding flow: prepare → apply for a token → (optionally) cancel it. See the [README's Agreement Payment section](../README.md#agreement-payment-stored-credential-auto-debit) for the end-to-end flow, and the [API Reference table](../README.md#api-reference) for the full endpoint list.

## prepare()

`POST /v1/authorizations/prepare`

Kicks off the binding flow and returns a URL for the user to authorize your app in TNG's Mini Program.

**Params**

| Key | Description |
| --- | --- |
| `referenceClientId` | Your Mini Program client ID |

`partnerId` is auto-injected from config — you don't need to pass it.

**Example**

```php
use Laraditz\TngEwallet\Facades\Tng;

$prepare = Tng::authorization()->prepare([
    'referenceClientId' => 'your-mini-program-client-id',
]);

// Redirect / hand $prepare->authURL to your Mini Program frontend.
```

**Response (`PrepareResponse`)**

| Property | Description |
| --- | --- |
| `authId` | TNG's identifier for this authorization attempt |
| `authURL` | URL to send the user to for authorization |
| `authClientId` | The client ID the authorization was issued against |

Plus the base fields shared by every response: `resultStatus`, `resultCode`, `resultMessage`, `isSuccessful()`, `isAccepted()`, `isFailed()`, `isUnknown()`, `raw()`, `toArray()`.

## applyToken()

`POST /v1/authorizations/applyToken`

Exchanges an `authCode` (from the user completing the `prepare()` flow) — or an existing `refreshToken` — for an access token.

**Params**

| Key | Description |
| --- | --- |
| `grantType` | `AUTHORIZATION_CODE` or `REFRESH_TOKEN` |
| `authCode` | Required when `grantType` is `AUTHORIZATION_CODE` |
| `referenceClientId` | Optional — stored alongside the resulting token row if present |

Note: unlike `prepare()`, `partnerId` is **not** auto-injected for this call.

**Example**

```php
$token = Tng::authorization()->applyToken([
    'grantType' => 'AUTHORIZATION_CODE',
    'authCode' => $authCodeFromMiniProgram,
]);

$token->accessToken;
$token->customerId;
```

**Response (`ApplyTokenResponse`)**

| Property | Description |
| --- | --- |
| `accessToken` | The issued access token |
| `accessTokenExpiryTime` | Expiry timestamp for `accessToken` |
| `refreshToken` | Token usable to obtain a new access token once this one expires |
| `refreshTokenExpiryTime` | Expiry timestamp for `refreshToken` |
| `customerId` | TNG's identifier for the authorizing user |

**Side effect:** every call inserts a **new** row into `tng_ewallet_access_tokens` (encrypted at rest via `TNG_ENCRYPTION_KEY`) — it never overwrites a previous row, so refreshing a token preserves history rather than mutating in place.

## cancelToken()

`POST /v1/authorizations/cancelToken`

Revokes a previously issued access token, ending the binding.

**Params**

| Key | Description |
| --- | --- |
| `accessToken` | The token to revoke |

**Example**

```php
Tng::authorization()->cancelToken([
    'accessToken' => $token->accessToken,
]);
```

**Response (`CancelTokenResponse`)**

No fields beyond the base response (`resultStatus`, `resultCode`, `resultMessage`, `isSuccessful()`, etc.) — a successful `resultStatus` confirms the revocation.

**Side effect:** the matching local `tng_ewallet_access_tokens` row (looked up by a hash of `accessToken`) is marked `status = cancelled` with `cancelled_at` set. It's not deleted.
