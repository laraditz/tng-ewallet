<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Models\Payment;

test('treats a resultStatus F ORDER_NOT_EXIST inquiry response as inquiry-failed, not a status answer', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false, 'tng-ewallet.default_return_url' => 'https://host-app.test']);

    Payment::create(['payment_request_id' => 'pr-f-1', 'status' => 'accepted']);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'F', 'resultCode' => 'ORDER_NOT_EXIST', 'resultMessage' => 'Order does not exist.'],
        'paymentStatus' => 'SUCCESS', // must be ignored - the API call itself was not confirmed successful
    ]), 200)]);

    $response = $this->get(route('tng-ewallet.return', ['payment_request_id' => 'pr-f-1']));

    $response->assertOk()
        ->assertSee("couldn't confirm this payment", false)
        ->assertDontSee('SUCCESS');
});

test('treats a resultStatus U inquiry response as inquiry-failed', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false, 'tng-ewallet.default_return_url' => 'https://host-app.test']);

    Payment::create(['payment_request_id' => 'pr-u-1', 'status' => 'accepted']);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'U', 'resultCode' => 'UNKNOWN_EXCEPTION', 'resultMessage' => 'unknown'],
    ]), 200)]);

    $response = $this->get(route('tng-ewallet.return', ['payment_request_id' => 'pr-u-1']));

    $response->assertOk()->assertSee("couldn't confirm this payment", false);
});
