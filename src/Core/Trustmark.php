<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Core;

use Bomsiwor\Trustmark\Contracts\ClientContract;
use Bomsiwor\Trustmark\Contracts\Resources\ClientFactoryInterface;
use Bomsiwor\Trustmark\Core\Factories\AntreanClientFactory;
use Bomsiwor\Trustmark\Core\Factories\VClaimClientFactory;
use InvalidArgumentException;

final class Trustmark
{
    /**
     * Membuat instance factory baru yang merupakan HTTP Client
     *
     * @return mixed
     */
    public static function client(string $service, array $config): ClientContract
    {
        $factory = self::getFactory($service);

        return $factory->createClient($config);
    }

    private static function getFactory(string $service): ClientFactoryInterface
    {
        return match ($service) {
            'vclaim' => new VClaimClientFactory,
            'antrean' => new AntreanClientFactory,
            default => throw new InvalidArgumentException('Service not supported')
        };
    }
}
