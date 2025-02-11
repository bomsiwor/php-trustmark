<?php

namespace Bomsiwor\Trustmark\Core\Factories;

use Bomsiwor\Trustmark\Contracts\ClientContract;
use Bomsiwor\Trustmark\Contracts\Resources\ClientFactoryInterface;
use Bomsiwor\Trustmark\Core\Clients\VClaimClient;
use Bomsiwor\Trustmark\Core\Decryptor\VClaimDecryptor;
use Bomsiwor\Trustmark\Core\Factory;
use Bomsiwor\Trustmark\Core\PackageValidator;
use Bomsiwor\Trustmark\Core\Signature\VClaimSignature;
use Respect\Validation\Validator as v;

final class VClaimClientFactory implements ClientFactoryInterface
{
    public function createClient(array $config): ClientContract
    {
        // Validate config
        $consId = $config['consId'] ?? null;
        $secretKey = $config['secretKey'] ?? null;
        $userKey = $config['userKey'] ?? null;
        $env = $config['env'] ??= 'production';

        self::validateConfig($config);

        // Construct decryptor
        $decryptor = new VClaimDecryptor($consId, $secretKey);

        // Generate signature untuk VClaim
        $signatureGenerator = VClaimSignature::generateSignature($consId, $secretKey);

        return self::factory(VClaimClient::class)
            ->withTimestamp($signatureGenerator->getTimestamp())
            ->withBaseUrl(self::getBaseUrl($env).'/'.self::getServiceUri($env))
            ->withConfig($config)
            ->withHttpHeader('X-signature', $signatureGenerator->getSignature())
            ->withHttpHeader('X-cons-id', $consId)
            ->withHttpHeader('X-timestamp', $signatureGenerator->getTimestamp())
            ->withHttpHeader('user_key', $userKey)
            ->withHttpHeader('Accept', 'application/json')
            ->withDecryptor($decryptor)
            ->make();
    }

    public static function factory(string $clientClass): Factory
    {
        return new Factory($clientClass);
    }

    private static function getServiceUri(string $env): string
    {
        return match ($env) {
            'production' => 'vclaim-rest',
            'development' => 'vclaim-rest-dev',
        };
    }

    private static function getBaseUrl(string $env): string
    {
        return match ($env) {
            'production' => 'https://apijkn.bpjs-kesehatan.go.id',
            'development' => 'https://apijkn-dev.bpjs-kesehatan.go.id',
        };
    }

    private static function validateConfig(array $options): void
    {
        $validator = [
            'env' => v::in(['production', 'development']),
            'consId' => v::stringType(),
            'secretKey' => v::stringType(),
            'userKey' => v::stringType(),
        ];

        PackageValidator::validate($options, $validator);
    }
}
