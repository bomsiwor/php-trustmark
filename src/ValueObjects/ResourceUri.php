<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\ValueObjects;

class ResourceUri
{
    private function __construct(private readonly string $format, private readonly array $data) {}

    public static function make(string $format, array $data = []): self
    {
        return new self($format, $data);
    }

    public function toString(): string
    {
        return sprintf($this->format, ...$this->data);
    }
}
