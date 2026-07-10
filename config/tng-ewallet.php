<?php

return [
    'sandbox' => env('TNG_SANDBOX', true),
    'base_url' => env('TNG_BASE_URL'),
    'sandbox_url' => 'https://api-sd.tngdigital.com.my',
    'production_url' => 'https://api.tngdigital.com.my',
    'client_id' => env('TNG_CLIENT_ID'),
    'partner_id' => env('TNG_PARTNER_ID'),
    'private_key_path' => env('TNG_PRIVATE_KEY_PATH', storage_path('tng/private.pem')),
    'public_key_path' => env('TNG_PUBLIC_KEY_PATH', storage_path('tng/tng_public.pem')),
    'key_version' => env('TNG_KEY_VERSION', 1),
    'verify_response_signature' => env('TNG_VERIFY_RESPONSE_SIGNATURE', true),
    'timeout' => env('TNG_TIMEOUT', 30),
    'notify_path' => env('TNG_NOTIFY_PATH', '/tng-ewallet/notify'),
    'encryption_key' => env('TNG_ENCRYPTION_KEY'),
];
