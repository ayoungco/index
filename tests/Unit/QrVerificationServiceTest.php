<?php

use App\Services\QrVerificationService;

test('qr payload matcher accepts raw uuid payload', function () {
    $service = new QrVerificationService;
    $uuid = '11111111-2222-3333-4444-555555555555';

    expect($service->payloadMatchesItem($uuid, $uuid))->toBeTrue();
});

test('qr payload matcher accepts canonical url payload', function () {
    $service = new QrVerificationService;
    $uuid = '11111111-2222-3333-4444-555555555555';
    $payload = 'https://index.ayoung.co/'.$uuid;

    expect($service->payloadMatchesItem($payload, $uuid))->toBeTrue();
});

test('qr payload matcher rejects mismatched payload', function () {
    $service = new QrVerificationService;

    expect(
        $service->payloadMatchesItem(
            'https://index.ayoung.co/aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
            '11111111-2222-3333-4444-555555555555'
        )
    )->toBeFalse();
});
