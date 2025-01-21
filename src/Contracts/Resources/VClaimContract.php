<?php

namespace Bomsiwor\Trustmark\Contracts\Resources;

interface VClaimContract
{
    /**
     * Mendapatkan Service name untuk setiap subservice VClaim
     */
    public function getServiceName(): string;

    public function getValidationRules(array $keys): array;

    public function createBody(string $key, mixed $data): mixed;
}
