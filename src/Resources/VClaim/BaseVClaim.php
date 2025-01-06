<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Resources\VClaim;

use Bomsiwor\Trustmark\Core\Decryptor\VClaimDecryptor;
use Bomsiwor\Trustmark\Core\PackageValidator;

class BaseVClaim
{
    /**
     * Validate the provided data using the given rules.
     *
     * @throws \Exception if validation fails.
     */
    protected function validate(array $data, array $rules): void
    {
        PackageValidator::validate($data, $rules);
    }

    protected function createDecryptor(string $consId, string $secretKey)
    {
        return new VClaimDecryptor($consId, $secretKey);
    }
}
