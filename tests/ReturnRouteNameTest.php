<?php

test('the return route is named tng-ewallet.return and accepts a payment_request_id query param', function () {
    expect(route('tng-ewallet.return', ['payment_request_id' => 'pr-1']))
        ->toContain('/tng-ewallet/return')
        ->toContain('payment_request_id=pr-1');
});
