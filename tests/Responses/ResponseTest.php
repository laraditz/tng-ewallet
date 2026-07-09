<?php

use Laraditz\TngEwallet\Responses\Response;

function makeResponseFixture(string $resultStatus): array
{
    return [
        'result' => [
            'resultStatus' => $resultStatus,
            'resultCode' => 'SOME_CODE',
            'resultMessage' => 'some message',
        ],
    ];
}

test('isSuccessful is true only for S', function () {
    $response = new Response(makeResponseFixture('S'));

    expect($response->isSuccessful())->toBeTrue()
        ->and($response->isAccepted())->toBeFalse()
        ->and($response->isFailed())->toBeFalse()
        ->and($response->isUnknown())->toBeFalse();
});

test('isAccepted is true only for A', function () {
    $response = new Response(makeResponseFixture('A'));

    expect($response->isAccepted())->toBeTrue()
        ->and($response->isSuccessful())->toBeFalse();
});

test('isFailed is true only for F', function () {
    $response = new Response(makeResponseFixture('F'));

    expect($response->isFailed())->toBeTrue()
        ->and($response->isSuccessful())->toBeFalse();
});

test('isUnknown is true only for U', function () {
    $response = new Response(makeResponseFixture('U'));

    expect($response->isUnknown())->toBeTrue()
        ->and($response->isSuccessful())->toBeFalse();
});

test('exposes resultStatus, resultCode, resultMessage', function () {
    $response = new Response(makeResponseFixture('S'));

    expect($response->resultStatus)->toBe('S')
        ->and($response->resultCode)->toBe('SOME_CODE')
        ->and($response->resultMessage)->toBe('some message');
});

test('raw and toArray round-trip the original payload', function () {
    $fixture = makeResponseFixture('S');
    $response = new Response($fixture);

    expect($response->raw())->toBe($fixture)
        ->and($response->toArray())->toBe($fixture);
});
