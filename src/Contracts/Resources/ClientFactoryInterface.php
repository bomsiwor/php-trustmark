<?php

namespace Bomsiwor\Trustmark\Contracts\Resources;

use Bomsiwor\Trustmark\Contracts\ClientContract;
use Bomsiwor\Trustmark\Core\Factory;

interface ClientFactoryInterface
{
    public function createClient(array $config): ClientContract;

    public static function factory(string $clientClass): Factory;
}
