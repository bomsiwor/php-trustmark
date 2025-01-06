<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Contracts;

/**
 * @internal
 */
interface StringableContract
{
    /**
     * Mengembalikan nilai string dari sebuah object
     */
    public function toString(): string;
}
