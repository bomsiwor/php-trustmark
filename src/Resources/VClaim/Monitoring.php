<?php

namespace Bomsiwor\Trustmark\Resources\VClaim;

use Bomsiwor\Trustmark\Contracts\Resources\VClaimContract;
use Bomsiwor\Trustmark\Core\PackageValidator;
use Bomsiwor\Trustmark\Enums\VClaim\JenisPelayananBPJSEnum;
use Bomsiwor\Trustmark\Enums\VClaim\StatusKlaimBPJSEnum;
use Bomsiwor\Trustmark\Responses\VClaimResponse;
use Bomsiwor\Trustmark\Transporters\HttpTransporter;
use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;
use Carbon\Carbon;
use DateTime;
use Respect\Validation\Validator as v;

final class Monitoring extends BaseVClaim implements VClaimContract
{
    public function __construct(private readonly HttpTransporter $transporter) {}

    public function getServiceName(): string
    {
        return 'Monitoring';
    }

    /**
     * Mendapatkan data riwayat kunjungan yang telah terkirim ke BPJS. Pada tanggal tertentu.
     *
     * @param  JenisPelayananBPJSEnum  $jenisPelayanan  Jenis pelayanan BPJS. Gunakan enum.
     * @param  string|null  $sepDate  Tanggal SEP. Default : today.
     * @return mixed
     */
    public function kunjungan(JenisPelayananBPJSEnum $jenisPelayanan, ?string $sepDate = null)
    {
        // Default sepDate
        $sepDate ??= Carbon::now()->format('Y-m-d');

        // Validate Data
        PackageValidator::validate([
            'sepDate' => $sepDate,
        ],
            [
                'sepDate' => 'required|string|date_format:Y-m-d|before_or_equal:today',
            ]);

        // Write Format URI
        $formatUri = sprintf('%s/Kunjungan/Tanggal/%s/JnsPelayanan/$s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName(), $sepDate, $jenisPelayanan->value]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($result, $this->transporter->getTimestamp());
    }

    /**
     * Mendapatkan data klaim BPJS yang sudah dikirimkan pada waktu tertentu
     *
     * @param  string  $tglPulang  Tanggal Pulang tertentu.
     * @param  JenisPelayananBPJSEnum  $jenisPelayanan  Jenis Pelayanan RI/RJ.
     * @param  StatusKlaimBPJSEnum  $statusKlaim-  Status Klaim. Gunakan enum.
     * @return mixed
     */
    public function klaim(string $tglPulang, JenisPelayananBPJSEnum $jenisPelayanan, StatusKlaimBPJSEnum $statusKlaim)
    {
        // Validate Data
        PackageValidator::validate([
            'tglPulang' => $tglPulang,
        ],
            [
                'tglPulang' => 'required|string|date_format:Y-m-d|before_or_equal:today',
            ]);

        // Write Format URI
        $formatUri = sprintf('%s/Klaim/Tanggal/%s/JnsPelayanan/%s/Status/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $tglPulang,
            $jenisPelayanan->value,
            $statusKlaim->value,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($result, $this->transporter->getTimestamp());
    }

    /**
     * Mendapatkan riwayat pelayanan peserta berdasarkan nomor BPJS dan interval waktu tertentu.
     *
     * @param  string  $noBpjs  Nomor BPJS Peserta
     * @param  string  $startDate  Tanggal mulai. Kurang dari hari ini.
     * @param  string  $endDate  Tanggal akhir, lebih dari startDate.
     * @return mixed
     */
    public function historyPelayananPeserta(string $noBpjs, ?string $startDate = null, ?string $endDate = null)
    {
        // Default value
        $startDate ??= (new DateTime)->format('Y-m-d');
        $endDate ??= (new DateTime)->format('Y-m-d');

        // Validate Data
        $data = compact('noBpjs', 'startDate', 'endDate');
        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        // Write Format URI
        $formatUri = '%s/HistoriPelayanan/NoKartu/%s/tglMulai/%s/tglAkhir/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $noBpjs,
            $startDate,
            $endDate,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($result, $this->transporter->getTimestamp());
    }

    /**
     * Monitoring Claim Jasa Raharja.
     *
     * @param  JenisPelayananBPJSEnum  $jenisPelayanan  Jenis Pelayanan RI/RJ. Gunakan Enum.
     * @param  string  $startDate  Tanggal awal. Kurangdari hari ini.
     * @param  string  $endDate  Tanggal akhir.
     * @return mixed
     */
    public function historyClaimJasaRaharja(JenisPelayananBPJSEnum $jenisPelayanan, string $startDate, string $endDate)
    {
        // Validate Data
        PackageValidator::validate([
            'startDate' => $startDate,
            'endDate' => $endDate,
        ],
            [
                'startDate' => 'required|date_format:Y-md|before_or_equal:today',
                'endDate' => 'required|date_format:Y-md|after:startDate',
            ]);

        // Write Format URI
        $formatUri = sprintf('%s/JasaRaharja/JnsPelayanan/%s/tglMulai/%s/tglAkhir/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $jenisPelayanan->value,
            $startDate,
            $endDate,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($result, $this->transporter->getTimestamp());
    }

    public function getValidationRules(array $keys): array
    {
        $rules = [
            'noBpjs' => v::stringType()->length(13, 15)->setName('Nomor BPJS'),
        ];

        return array_intersect_key($rules, array_flip($keys));
    }
}
