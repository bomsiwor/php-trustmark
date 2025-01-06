<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\ValueObjects\Transporter;

use Bomsiwor\Trustmark\Contracts\StringableContract;
use Exception;

/**
 * @internal
 */
final class BaseUrl implements StringableContract
{
    /**
     * Creates a new Base URI value object.
     */
    private function __construct(private readonly string $baseUri)
    {
        // ..
    }

    /**
     * Creates a new Base URI value object.
     */
    public static function from(string $baseUri): self
    {
        return new self($baseUri);
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        if (str_starts_with($this->baseUri, 'https')) {
            return "{$this->baseUri}/";
        }

        if (str_starts_with($this->baseUri, 'http')) {
            throw new Exception('Cannot use non SSL URI');
        }

        return "https://{$this->baseUri}/";
    }
}
