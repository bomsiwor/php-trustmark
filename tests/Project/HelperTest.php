<?php

it('can convert null to empty string', function () {
    $result = convertBooleanToBpjsString(null);

    expect($result)->toBe('');
});

it('can convert true to string', function () {
    $result = convertBooleanToBpjsString(true);

    expect($result)->toBe('1');
});

it('can convert false to string', function () {
    $result = convertBooleanToBpjsString(false);

    expect($result)->toBe('0');
});
