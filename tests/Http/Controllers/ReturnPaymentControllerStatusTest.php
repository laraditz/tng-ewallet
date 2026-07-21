<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Models\Payment;

test('shows the live inquiry() status, amount, paymentRequestId, and date/time for a matched payment', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Payment::create([
        'payment_request_id' => 'pr-status-1',
        'status' => 'accepted',
        'customer_return_url' => 'https://host-app.test/thanks',
    ]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'paymentId' => 'pay-status-1',
        'paymentRequestId' => 'pr-status-1',
        'paymentAmount' => ['currency' => 'MYR', 'value' => '10000'],
        'paymentTime' => '2026-07-21T10:00:00+08:00',
        'paymentStatus' => 'SUCCESS',
        'paymentFailReason' => null,
    ]), 200)]);

    $response = $this->get(route('tng-ewallet.return', ['payment_request_id' => 'pr-status-1']));

    $response->assertOk()
        ->assertSee('SUCCESS')
        ->assertSee('MYR')
        ->assertSee('10000')
        ->assertSee('pr-status-1')
        ->assertSee('2026-07-21T10:00:00+08:00')
        ->assertSee('https://host-app.test/thanks', false)
        ->assertDontSee('fail-reason-marker');
});

test('shows the fail reason when the inquiry response includes one', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false, 'tng-ewallet.default_return_url' => 'https://host-app.test']);

    Payment::create([
        'payment_request_id' => 'pr-status-2',
        'status' => 'failed',
    ]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'paymentId' => 'pay-status-2',
        'paymentRequestId' => 'pr-status-2',
        'paymentAmount' => ['currency' => 'MYR', 'value' => '5000'],
        'paymentTime' => '2026-07-21T11:00:00+08:00',
        'paymentStatus' => 'FAIL',
        'paymentFailReason' => 'Insufficient balance',
    ]), 200)]);

    $response = $this->get(route('tng-ewallet.return', ['payment_request_id' => 'pr-status-2']));

    $response->assertOk()
        ->assertSee('FAIL')
        ->assertSee('Insufficient balance')
        // no customer_return_url on this Payment row, so back link falls back to the configured default
        ->assertSee('https://host-app.test', false);
});
