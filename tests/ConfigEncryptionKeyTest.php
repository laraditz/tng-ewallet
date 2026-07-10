<?php

test('encryption_key config resolves from TNG_ENCRYPTION_KEY env', function () {
    expect(config('tng-ewallet.encryption_key'))->toBe('base64:8QzJmR2vN5tK9pL3xW7bC1dF6hY0aE4iO8uT2sG5jM0=');
});
