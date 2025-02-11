<?php

use Bomsiwor\Trustmark\Contracts\DecryptorContract;
use Bomsiwor\Trustmark\Contracts\TransporterContract;
use Bomsiwor\Trustmark\Core\Clients\AntreanClient;

function mockAntreanClient(mixed $data): AntreanClient
{
    // Encode the data to simulate decrypt obfuscated data

    // Mock client
    // by Mocking Transporter
    $transporter = mock(TransporterContract::class);

    // Because the decryptor is not null
    $transporter
        ->shouldNotReceive('getConfig')
        ->shouldReceive('getTimestamp')
        ->once()
        ->shouldReceive('sendRequest')
        ->once()
        ->andReturn($data);

    // Mock Decryptor
    $decryptor = mock(DecryptorContract::class);

    $decryptor
        ->shouldReceive('decryptData')
        ->andReturnSelf()
        ->shouldReceive('result')
        ->andReturn(json_decode($data['response'], true), true);

    $client = new AntreanClient($transporter, $decryptor);

    return $client;
}
