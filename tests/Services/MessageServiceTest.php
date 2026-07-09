<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Responses\SendMessageResponse;
use Laraditz\TngEwallet\Services\MessageService;

test('sendByAccessToken() posts to /v2/customers/message/sendByAccessToken and returns a SendMessageResponse', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
    ]), 200)]);

    $service = new MessageService(new TngClient());
    $response = $service->sendByAccessToken(['accessToken' => 'tok_abc', 'message' => 'Hello']);

    expect($response)->toBeInstanceOf(SendMessageResponse::class)
        ->and($response->isSuccessful())->toBeTrue();

    Http::assertSent(fn ($request) => $request->url() === 'https://example.test/v2/customers/message/sendByAccessToken'
        && $request['accessToken'] === 'tok_abc');
});
