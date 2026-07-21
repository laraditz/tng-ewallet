<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Models\Payment;

test('shows a generic inquiry-failed state when inquiry() throws', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false, 'tng-ewallet.default_return_url' => 'https://host-app.test']);

    $payment = Payment::create([
        'payment_request_id' => 'pr-exception-1',
        'status' => 'accepted',
    ]);

    Http::fake(['https://example.test/*' => fn () => throw new \Illuminate\Http\Client\ConnectionException('could not connect')]);

    $response = $this->get(route('tng-ewallet.return', ['payment_request_id' => 'pr-exception-1']));

    $response->assertOk()
        ->assertSee("couldn't confirm this payment", false)
        ->assertSee('https://host-app.test', false);

    expect($payment->fresh()->status->value)->toBe('accepted');
});
