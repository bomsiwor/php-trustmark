<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Core;

use Bomsiwor\Trustmark\Contracts\ClientContract;
use Bomsiwor\Trustmark\Core\Clients\VClaimClient;
use Bomsiwor\Trustmark\Core\Signature\VClaimSignature;
use Respect\Validation\Validator as v;

final class Trustmark
{
    /**
     * Membuat instance factory baru yang merupakan HTTP Client
     *
     * @return mixed
     */
    public static function client(string $consId, string $secretKey, string $userKey, string $service, string $env = 'production'): ClientContract
    {
        // Validate config
        $config = compact('consId', 'secretKey', 'userKey', 'env');

        self::validateConfig($config);

        // Generate signature untuk VClaim
        $signatureGenerator = VClaimSignature::generateSignature($consId, $secretKey);

        // Generate client class
        $clientClass = self::getClientClass($service);

        return self::factory($clientClass)
            ->withTimestamp($signatureGenerator->getTimestamp())
            ->withBaseUrl(self::getBaseUrl($env).'/'.self::getServiceUri($service, $env))
            ->withConfig($config)
            ->withHttpHeader('X-signature', $signatureGenerator->getSignature())
            ->withHttpHeader('X-cons-id', $consId)
            ->withHttpHeader('X-timestamp', $signatureGenerator->getTimestamp())
            ->withHttpHeader('user_key', $userKey)
            ->withHttpHeader('Accept', 'application/json')
            ->make();
    }

    public static function factory(string $clientClass): Factory
    {
        return new Factory($clientClass);
    }

    private static function validateConfig(array $options): void
    {
        $validator = [
            'env' => v::in(['production', 'development']),
            'consId' => v::stringType(),
            'secretKey' => v::stringType(),
            'userKey' => v::stringType(),
        ];

        // PackageValidator::validate($validator, $options);
    }

    private static function getServiceUri(string $service, string $env)
    {
        return match ($service.'.'.$env) {
            'vclaim.production' => 'vclaim-rest',
            'vclaim.development' => 'vclaim-rest-dev',
        };
    }

    private static function getBaseUrl(string $env)
    {
        return match ($env) {
            'production' => 'https://apijkn.bpjs-kesehatan.go.id',
            'development' => 'https://apijkn-dev.bpjs-kesehatan.go.id',
        };
    }

    public static function getClientClass(string $service): ?string
    {
        // $validator = [
        //     'service' => v::in(['vclaim']),
        // ];
        //
        // PackageValidator::validate(compact($service), $validator);

        return match ($service) {
            'vclaim' => VClaimClient::class,
            default => null,
        };
    }
}
