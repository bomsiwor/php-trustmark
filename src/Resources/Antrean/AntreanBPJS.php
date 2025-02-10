<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Resources\Antrean;

use Bomsiwor\Trustmark\Contracts\DecryptorContract;
use Bomsiwor\Trustmark\Contracts\Resources\AntreanContract;
use Bomsiwor\Trustmark\Contracts\TransporterContract;
use Bomsiwor\Trustmark\Responses\VClaimResponse;
use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;
use DateTime;
use Respect\Validation\Validator as v;

final class Peserta extends BaseWSAntrean implements AntreanContract
{
    public function __construct(private readonly TransporterContract $transporter, private readonly DecryptorContract $decryptor) {}

    public function getServiceName(string $key = ''): string
    {
        return match ($key) {
            'ref' => 'ref',
            'dash' => 'dashboard',
            default => 'antrean',
        };
    }

    public function poli()
    {
        // Write Format URIs
        $formatUri = '%s/poli';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName('ref')]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function dokter()
    {
        // Write Format URI
        $formatUri = '%s/dokter';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName('ref')]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function jadwalDokter(string $kodePoli, string $tanggal)
    {
        // Write Format URI
        $formatUri = 'jadwaldokter/kodepoli/%s/tanggal/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$kodePoli, $tanggal]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function poliFingerprint()
    {
        // Write Format URI
        $formatUri = '%s/poli/fp';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName('ref')]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    // TODO: add validation for args
    public function pasienFingerprint(string $tipeIdentitas, string $noIdentitas)
    {
        // Write Format URI
        $formatUri = '%s/pasien/fp/identitas/%s/noidentitas/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName('ref'), $tipeIdentitas, $noIdentitas]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function updateJadwalDokter(array $data)
    {
        // Write Format URI
        $formatUri = 'jadwaldokter/updatejadwaldokter';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName('ref')]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function taskIDList()
    {
        // Write Format URI
        $formatUri = '%s/getlisttask';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName()]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function dashboardPerDate(string $tanggal, string $waktu)
    {
        // Write Format URI
        $formatUri = '%s/waktutunggu/tanggal/%s/waktu/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName('dash'), $tanggal, $waktu]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function dashboardPerBulan(int $bulan, int $tahun, string $waktu)
    {
        // Write Format URI
        $formatUri = '%s/waktutunggu/bulan/%s/tahun/%d/waktu/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName('dash'), $bulan, $tahun, $waktu]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function perTanggal(string $tanggal)
    {
        // Write Format URI
        $formatUri = '%s/pendaftaran/tanggal/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName(), $tanggal]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function perKodeBooking(string $kodeBooking)
    {
        // Write Format URI
        $formatUri = '%s/pendaftaran/kodebooking/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName(), $kodeBooking]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function belumDilayani()
    {
        // Write Format URI
        $formatUri = '%s/pendaftaran/aktif';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName()]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function belumDilayaniPerPoli(string $kodePoli, string $kodeDokter, string $hari, string $jamPraktik)
    {
        // Write Format URI
        $formatUri = '%s/pendaftaran/kodepoli/%s/kodedokter/%s/hari/%s/waktu/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName(), $kodePoli, $kodeDokter, $hari, $jamPraktik]);

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
