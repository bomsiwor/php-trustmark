<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Resources\VClaim;

use Bomsiwor\Trustmark\Contracts\DecryptorContract;
use Bomsiwor\Trustmark\Contracts\Resources\VClaimContract;
use Bomsiwor\Trustmark\Contracts\TransporterContract;
use Bomsiwor\Trustmark\Responses\VClaimResponse;
use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;
use DateTime;
use Respect\Validation\Validator as v;

final class Peserta extends BaseVClaim implements VClaimContract
{
    public function __construct(private readonly TransporterContract $transporter, private readonly DecryptorContract $decryptor) {}

    public function getServiceName(): string
    {
        return 'Peserta';
    }

    /**
     * Mendapatkan data peserta berdasarkan NIK.
     *
     * @param  string  $nik  Valid NIK 16 digit
     * @param  string  $sepDate  Tgl SEP. Default : current date
     * @return mixed Data Peserta
     */
    public function byNIK(string $nik, ?string $sepDate = null)
    {
        // Default sepDate
        $sepDate ??= (new DateTime)->format('Y-m-d');

        // Validation
        $rules = $this->getValidationRules(['nik', 'sepDate']);
        $this->validate(compact('nik', 'sepDate'), $rules);

        // Write Format URI
        $formatUri = '%s/nik/%s/tglSEP/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName(), $nik, $sepDate]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Mendapatkan data Peserta berdasarkan Nomor BPJS
     *
     * @param  string  $noBpjs  Nomor BPJS
     * @param  string  $sepDate  Tanggal SEP (Y-m-d)
     * @return mixed Data Peserta
     */
    public function byNomorBPJS(string $noBpjs, ?string $sepDate = null)
    {
        // Default sepDate
        $sepDate ??= (new DateTime)->format('Y-m-d');

        // Write format URI
        $formatUri = '%s/nokartu/%s/tglSEP/%s';

        // Validate data
        $rules = $this->getValidationRules(['noBpjs', 'sepDate']);
        $this->validate(compact('noBpjs', 'sepDate'), $rules);

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName(), $noBpjs, $sepDate]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function getValidationRules(array $keys): array
    {
        $rules = [
            'nik' => v::stringType()->length(16, 16)->setName('NIK'),
            'noBpjs' => v::stringType()->length(13, 15)->setName('Nomor BPJS'),
            'sepDate' => v::date('Y-m-d')
                ->oneOf(
                    v::lessThan((new DateTime)->format('Y-m-d')),
                    v::equals((new DateTime)->format('Y-m-d'))
                ),
        ];

        return array_intersect_key($rules, array_flip($keys));
    }
}
