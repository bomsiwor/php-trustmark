<?php

use Bomsiwor\Trustmark\Core\Trustmark;
use Bomsiwor\Trustmark\Resources\Antrean\AntreanBPJS;

beforeEach(function () {
    // Initiate client
    $consId = '123abc';
    $secretKey = 'secretAlways';
    $userKey = 'userkey';

    // Create vclaim client
    $this->client = Trustmark::client('antrean', compact('consId', 'secretKey', 'userKey'));
});

it('has bpjs subservice', function () {
    expect($this->client->bpjs())->toBeInstanceOf(AntreanBPJS::class);
});
