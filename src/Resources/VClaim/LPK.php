<?php

namespace Bomsiwor\Trustmark\Resources\VClaim;

use Bomsiwor\Trustmark\Contracts\VClaimContract;
use Bomsiwor\Trustmark\Core\PackageValidator;
use Bomsiwor\Trustmark\Enums\VClaim\JenisPelayananBPJSEnum;
use Bomsiwor\Trustmark\Responses\VClaimResponse;
use Bomsiwor\Trustmark\Transporters\HttpTransporter;
use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;

final class LPK implements VClaimContract
{
    public function __construct(private readonly HttpTransporter $transporter) {}

    public function getServiceName(): string
    {
        return 'LPK';
    }

    public function list(string $tglMasuk, JenisPelayananBPJSEnum $jenisPelayanan)
    {
        // Validate Data
        PackageValidator::validate([
            'tglMasuk' => $tglMasuk,
        ],
            [
                'tglMasuk' => 'required|string|date_format:Y-m-d|before_or_equal:today',
            ]);

        // Write Format URI
        $formatUri = sprintf('%s/TglMasuk/%s/JnsPelayanan/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName(), $tglMasuk, $jenisPelayanan->value]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($result, $this->transporter->getTimestamp());
    }
}
