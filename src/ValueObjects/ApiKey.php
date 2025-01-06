<?php

namespace Bomsiwor\Trustmark\ValueObjects;

use Bomsiwor\Trustmark\Contracts\StringableContract;

final class ApiKey implements StringableContract
{
    private function __construct(public readonly string $apiKey) {}

    public static function from(string $apiKey): self
    {
        return new self($apiKey);
    }

    public function toString(): string
    {
        return $this->apiKey;
    }
}
