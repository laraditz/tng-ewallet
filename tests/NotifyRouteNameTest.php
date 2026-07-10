<?php

test('the notify route is named tng-ewallet.notify, so route() resolves it', function () {
    expect(route('tng-ewallet.notify'))->toContain('/tng-ewallet/notify');
});
