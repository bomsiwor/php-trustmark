<?php

use Bomsiwor\Trustmark\Core\PackageValidator;
use Bomsiwor\Trustmark\Exceptions\VClaimException;
use Respect\Validation\Validator as v;

it('can pass validate using vclaim validator', function () {
    $data = [
        'noBpjs' => '0012345',
        'tglKeluar' => '2024-12-01',
    ];

    // Validation rules
    $rules = [
        'noBpjs' => v::stringType()->setName('No BPJS'),
        'tglKeluar' => v::date('Y-m-d')->lessThan('2024-12-31'),
    ];

    expect(PackageValidator::validate($data, $rules))->toBeTrue();
});

it('can throw exception using vclaim validator', function () {
    $data = [
        'noBpjs' => '0012345',
        'tglKeluar' => '2025-12-01',
    ];

    // Validation rules
    $rules = [
        'noBpjs' => v::intType()->setName('No BPJS'),
        'tglKeluar' => v::date('Y-m-d')->lessThan('2024-12-31'),
    ];

    expect(fn () => PackageValidator::validate($data, $rules))->toThrow(VClaimException::class);
});
