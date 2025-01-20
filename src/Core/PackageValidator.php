<?php

namespace Bomsiwor\Trustmark\Core;

use Bomsiwor\Trustmark\Exceptions\TrustmarkException;
use Exception;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

final class PackageValidator
{
    public function __construct(protected array $data, protected array $validator) {}

    public static function validate(array $data, array $rules): bool
    {
        try {

            $validator = new v;

            // Loop through data and validation rules
            foreach ($rules as $key => $rule) {
                $validator->key($key, $rule);
            }

            // Throw new Exception on invalidat data
            $validator->assert($data);

            return true;

        } catch (NestedValidationException $e) {
            throw new TrustmarkException($e->getMessage(), 'Local/Validation', $e->getMessages());
        }
    }

    public function check(): bool
    {

        $validator = new v;

        // Loop through data and validation rules
        foreach ($this->validator as $key => $value) {
            $validator->key($key, $value);
        }

        $valid = $validator->validate($this->data);
        echo $valid;

        return $valid;
    }
}
