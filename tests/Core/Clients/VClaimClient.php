<?php

use Bomsiwor\Trustmark\Core\Trustmark;
use Bomsiwor\Trustmark\Resources\VClaim\Referensi;
use Bomsiwor\Trustmark\Resources\VClaim\SEP;

beforeEach(function () {
    // Initiate client
    $consId = '123abc';
    $secretKey = 'secretAlways';
    $userKey = 'userkey';

    // Create vclaim client
    $this->client = Trustmark::client('vclaim', compact('consId', 'secretKey', 'userKey'));
});

it('has referensi subservice', function () {
    expect($this->client->referensi())->toBeInstanceOf(Referensi::class);
});

it('has sep subservice', function () {
    expect($this->client->sep())->toBeInstanceOf(SEP::class);
});
