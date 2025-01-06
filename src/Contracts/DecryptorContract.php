<?php

namespace Bomsiwor\Trustmark\Contracts;

interface DecryptorContract
{
    public function decryptData(string $timestamp, string $obfuscatedData): self;

    public function result(): mixed;
}
