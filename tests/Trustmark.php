<?php

use Bomsiwor\Trustmark\Core\Clients\AntreanClient;
use Bomsiwor\Trustmark\Core\Clients\VClaimClient;
use Bomsiwor\Trustmark\Core\Trustmark;
use Bomsiwor\Trustmark\Exceptions\TrustmarkException;

pest()->group('client-factory');

// Scoped variable on this test
$consId = '123abc';
$secretKey = 'secretAlways';
$userKey = 'userkey';

it('throw exception on invalid service', function () {
    $client = Trustmark::client('random', []);
})->throws(InvalidArgumentException::class, 'Service not supported');

// ===============
// VCLAIM BLOCK
// ===============

it('on vclaim - create client', function () use ($consId, $secretKey, $userKey) {
    $client = Trustmark::client('vclaim', [
        'consId' => $consId,
        'secretKey' => $secretKey,
        'userKey' => $userKey,
    ]);

    expect($client)->toBeInstanceOf(VClaimClient::class);
});

it('on vclaim - throw validation error on invalid config', function () {
    $client = Trustmark::client('vclaim', ['conf' => 'random']);
})->throws(TrustmarkException::class);

// ===============
// VCLAIM BLOCK
// ===============

it('on antrean - create client', function () use ($consId, $secretKey, $userKey) {
    $client = Trustmark::client('antrean', [
        'consId' => $consId,
        'secretKey' => $secretKey,
        'userKey' => $userKey,
    ]);

    expect($client)->toBeInstanceOf(AntreanClient::class);
});
