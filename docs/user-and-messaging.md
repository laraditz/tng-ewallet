# User and messaging

`Tng::user()->...` and `Tng::message()->...` — both operate on a user identified by an access token from the [Agreement Payment binding flow](authorization.md). See the [README's User info and messaging section](../README.md#user-info-and-messaging) and the [API Reference table](../README.md#api-reference) for the full endpoint list.

## user()->inquiryByAccessToken()

`POST /v1/customers/user/inquiryUserInfoByAccessToken`

Fetches the TNG user's profile info for a given access token.

**Params**

| Key | Description |
| --- | --- |
| `accessToken` | An access token obtained via `Tng::authorization()->applyToken()` |

**Example**

```php
use Laraditz\TngEwallet\Facades\Tng;

$userInfo = Tng::user()->inquiryByAccessToken([
    'accessToken' => $token->accessToken,
]);

$userInfo->userInfo; // raw array, e.g. ['userId' => '...']
```

**Response (`UserInfoResponse`)**

| Property | Description |
| --- | --- |
| `userInfo` | Raw associative array of user fields returned by TNG (e.g. `userId`) |

**Side effect:** `updateOrCreate`s a `TngUser` row (`tng_ewallet_users`) keyed by `userInfo['userId']`, storing the raw `user_info` JSON, `last_fetched_at`, and linking it to the matching `AccessToken` row.

## message()->sendByAccessToken()

`POST /v2/customers/message/sendByAccessToken`

Sends a message to the user associated with an access token.

**Params**

| Key | Description |
| --- | --- |
| `accessToken` | An access token obtained via `Tng::authorization()->applyToken()` |
| `message` | The message text to send |

**Example**

```php
Tng::message()->sendByAccessToken([
    'accessToken' => $token->accessToken,
    'message' => 'Your order has shipped!',
]);
```

**Response (`SendMessageResponse`)**

No fields beyond the base response (`resultStatus`, `resultCode`, `resultMessage`, `isSuccessful()`, etc.) — no local persistence side effect.
