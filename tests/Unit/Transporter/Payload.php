<?php

use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;
use Psr\Http\Message\RequestInterface;

it('can create get request instance', function () {
    $payload = Payload::get('/api/vclaim/test-uri/%s', ['pathVariable']);

    $request = $payload->toRequest('http://vclaim-api.com', [
        'Accept' => 'application/json',
    ]);

    expect($request)->toBeObject()->toBeInstanceOf(RequestInterface::class);
});

it('can create insert request instance', function () {
    $baseUrl = 'http://vclaim-api.com';

    $data = [
        'name' => 'John Doe',
    ];

    $payload = Payload::insert('/api/vclaim/test-uri/%s', ['pathVariable'], $data);

    $request = $payload->toRequest($baseUrl, [
        'Accept' => 'application/json',
    ]);

    // Test several properties
    // Path
    expect($request->getUri()->getPath())->toBeString()->toBe('/api/vclaim/test-uri/pathVariable');

    // Body
    expect(json_decode($request->getBody()->getContents(), true))->toMatchArray($data);

});
