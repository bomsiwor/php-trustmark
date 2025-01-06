<?php

use Bomsiwor\Trustmark\ValueObjects\ResourceUri;
use Bomsiwor\Trustmark\ValueObjects\Transporter\BaseUrl;

it('can generate base url from string', function () {
    $uri = BaseUrl::from('vclaim.com/api');

    $uri = $uri->toString();

    expect($uri)->toBeString()->toBe('https://vclaim.com/api/');
});

it('cannot use non ssl protocol', function () {
    $uri = BaseUrl::from('http://vclaim.com/api');

    expect(fn () => $uri->toString())->toThrow(Exception::class, 'Cannot use non SSL URI');
});

it('can generate resource URI with formatted string', function () {
    $resourceName = 'Peserta';

    $resourceUri = ResourceUri::make('%s/nik/%s', [$resourceName, '12345678']);

    $uri = BaseUrl::from('vclaim.com/api')->toString().$resourceUri->toString();

    expect($uri)->toBeString()->toBe('https://vclaim.com/api/Peserta/nik/12345678');
});
