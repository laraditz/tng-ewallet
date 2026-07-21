<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Models\Payment;

test('the return route never writes to the Payment record, across all three states', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false, 'tng-ewallet.default_return_url' => 'https://host-app.test']);

    $success = Payment::create(['payment_request_id' => 'pr-nowrite-success', 'status' => 'accepted']);
    $failed = Payment::create(['payment_request_id' => 'pr-nowrite-failed', 'status' => 'accepted']);

    $beforeSuccess = $success->fresh()->getAttributes();
    $beforeFailed = $failed->fresh()->getAttributes();
    $countBefore = Payment::count();

    // not-found state - no Payment row should be touched or created
    $this->get(route('tng-ewallet.return', ['payment_request_id' => 'pr-does-not-exist']))->assertOk();
    expect(Payment::count())->toBe($countBefore);

    // found + inquiry() success state
    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'paymentStatus' => 'SUCCESS',
        'paymentAmount' => ['currency' => 'MYR', 'value' => '100'],
        'paymentTime' => '2026-07-21T10:00:00+08:00',
    ]), 200)]);
    $this->get(route('tng-ewallet.return', ['payment_request_id' => 'pr-nowrite-success']))->assertOk();
    expect($success->fresh()->getAttributes())->toBe($beforeSuccess);

    // found + inquiry() failed state
    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'U', 'resultCode' => 'UNKNOWN_EXCEPTION', 'resultMessage' => 'unknown'],
    ]), 200)]);
    $this->get(route('tng-ewallet.return', ['payment_request_id' => 'pr-nowrite-failed']))->assertOk();
    expect($failed->fresh()->getAttributes())->toBe($beforeFailed);

    expect(Payment::count())->toBe($countBefore);
});
