<?php

test('shows a friendly not-found state when payment_request_id matches no Payment row', function () {
    config(['tng-ewallet.default_return_url' => 'https://host-app.test']);

    $response = $this->get(route('tng-ewallet.return', ['payment_request_id' => 'no-such-payment']));

    $response->assertOk()
        ->assertSee("couldn't find this payment", false)
        ->assertSee('https://host-app.test', false);
});

test('shows the same not-found state when payment_request_id is missing entirely', function () {
    config(['tng-ewallet.default_return_url' => 'https://host-app.test']);

    $response = $this->get(config('tng-ewallet.return_path'));

    $response->assertOk()
        ->assertSee("couldn't find this payment", false)
        ->assertSee('https://host-app.test', false);
});
