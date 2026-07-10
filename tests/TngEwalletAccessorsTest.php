<?php

use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Services\AuthorizationService;
use Laraditz\TngEwallet\Services\MessageService;
use Laraditz\TngEwallet\Services\PaymentService;
use Laraditz\TngEwallet\Services\RefundService;
use Laraditz\TngEwallet\Services\UserService;
use Laraditz\TngEwallet\TngEwallet;

test('each accessor returns the correct service instance', function () {
    $manager = app(TngEwallet::class);

    expect($manager->authorization())->toBeInstanceOf(AuthorizationService::class)
        ->and($manager->user())->toBeInstanceOf(UserService::class)
        ->and($manager->payment())->toBeInstanceOf(PaymentService::class)
        ->and($manager->refund())->toBeInstanceOf(RefundService::class)
        ->and($manager->message())->toBeInstanceOf(MessageService::class)
        ->and($manager->client())->toBeInstanceOf(TngClient::class);
});

test('client() returns the same bound TngClient singleton', function () {
    $manager = app('tng-ewallet');

    expect($manager->client())->toBe(app(TngClient::class));
});
