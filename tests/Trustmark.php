<?php

use Bomsiwor\Trustmark\Contracts\ClientContract;
use Bomsiwor\Trustmark\Core\Clients\VClaimClient;
use Bomsiwor\Trustmark\Core\Trustmark;
use Bomsiwor\Trustmark\Exceptions\TrustmarkException;

// Scoped variable on this test
$consId = '123abc';
$secretKey = 'secretAlways';
$userKey = 'userkey';

it('create a client based on service id', function (string $serviceId, string $serviceClass) use ($consId, $userKey, $secretKey) {

    $trustmarkClient = Trustmark::client($consId, $secretKey, $userKey, $serviceId, 'production');

    expect($trustmarkClient)->toBeInstanceOf(ClientContract::class)->toBeInstanceOf($serviceClass);
})
    ->with([
        ['vclaim', VClaimClient::class],
    ]);

it('throw error on invalid service id', function () use ($consId, $userKey, $secretKey) {
    Trustmark::client($consId, $secretKey, $userKey, 'random', 'production');
})->throws(TrustmarkException::class);

it('create a client via factory', function () {
    $trustmarkClient = Trustmark::factory(VClaimClient::class)
        ->withBaseUrl('http://trustmark.test')
        ->withTimestamp(time())
        ->make();

    expect($trustmarkClient)->toBeInstanceOf(ClientContract::class)->toBeInstanceOf(VClaimClient::class);
});
