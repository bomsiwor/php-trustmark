<?php

namespace Bomsiwor\Trustmark\Resources\VClaim;

use Bomsiwor\Trustmark\Contracts\VClaimContract;
use Bomsiwor\Trustmark\Core\PackageValidator;
use Bomsiwor\Trustmark\Responses\VClaimResponse;
use Bomsiwor\Trustmark\Transporters\HttpTransporter;
use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;

final class PRB implements VClaimContract
{
    public function __construct(private readonly HttpTransporter $transporter) {}

    public function getServiceName(): string
    {
        return 'prb';
    }

    /**
     * Pencarian data PRB berdasarkan nomor SRB
     *
     * @param  string  $srbNumber  Nomor SRB
     * @param  string  $noSep  NOmor SEP yang digunakan untuk membuat SRB
     * @return mixed
     */
    public function listBySRB(string $srbNumber, string $noSep)
    {
        // Validate Data
        PackageValidator::validate([
            'srbNumber' => $srbNumber,
            'noSep' => $noSep,
        ],
            [
                'srbNumber' => 'required|string',
                'noSep' => 'required|string|size:19',
            ]);

        // Write Format URI
        $formatUri = sprintf('%s/%s/nosep/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $srbNumber,
            $noSep,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data PRB berdasarkan interval tanggal tertentu.
     *
     * @param  string  $startDate  Tanggal mulai. Kurang dari hari ini.
     * @param  string  $endDate  Tanggal akhir. Lebih dari tanggal mulai.
     * @return mixed
     */
    public function listByDate(string $startDate, string $endDate)
    {
        // Validate Data
        PackageValidator::validate([
            'startDate' => $startDate,
            'endDate' => $endDate,
        ],
            [
                'startDate' => 'required|string|date_format:Y-m-d|before_or_equal:today',
                'endDate' => 'required|string|date_format:Y-m-d|after_or_equal:startDate',
            ]);

        // Write Format URI
        $formatUri = sprintf('%s/tglMulai/%s/tglAkhir/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $startDate,
            $endDate,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($result, $this->transporter->getTimestamp());
    }
}
