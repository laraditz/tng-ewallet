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
