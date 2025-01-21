<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Resources\VClaim;

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

    public function createBody(string $key, mixed $data): mixed
    {
        return [];
    }
}
