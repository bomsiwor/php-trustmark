<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Resources\VClaim;

use Bomsiwor\Trustmark\Contracts\Resources\VClaimContract;
use Bomsiwor\Trustmark\Enums\VClaim\JenisFilterRencanaKontrol;
use Bomsiwor\Trustmark\Enums\VClaim\JenisFilterRencanaKontrolEnum;
use Bomsiwor\Trustmark\Enums\VClaim\JenisKontrolEnum;
use Bomsiwor\Trustmark\Responses\VClaimResponse;
use Bomsiwor\Trustmark\Transporters\HttpTransporter;
use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;
use DateTime;
use Respect\Validation\Validator as v;

final class RencanaKontrol extends BaseVClaim implements VClaimContract
{
    public function __construct(private readonly HttpTransporter $transporter) {}

    public function getServiceName(): string
    {
        return 'RencanaKontrol';
    }

    /**
     * Melihat data Surat Kontrol berdasarkan Nomor Surat Kontrol
     *
     * @param  string  $noKontrol  Nomor Surat Kontrol
     * @return mixed
     */
    public function cari(string $noKontrol)
    {
        // Validate inputs
        $data = compact('noKontrol');
        $rules = $this->getValidationRules(['noKontrol']);
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf('%s/noSuratKontrol/%s', $this->getServiceName(), $noKontrol);
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($result, $this->transporter->getTimestamp());
    }

    /**
     * Melihat data SEP untuk keperluan rencana kontrol.
     *
     * @param  string  $noSep  Nomor SEP
     * @return mixed
     */
    public function cariSEP(string $noSep)
    {
        // Validate inputs
        $data = compact('noSep');
        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf('%s/nosep/%s', $this->getServiceName(), $noSep);
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($result, $this->transporter->getTimestamp());
    }

    /**
     * Data rencana kontrol by nomor BPJS Peserta
     *
     * @param  string  $noBpjs  Nomor BPJS.
     * @param  JenisFilterRencanaKontrol  $jenisFilter  Jenis Filter pencarian data (Enum)
     * @param  int|null  $bulan  Bulan (1-12)
     * @param  int|null  $tahun  Tahun. Min : 2000.
     * @return mixed
     */
    public function listByNoBPJS(string $noBpjs, JenisFilterRencanaKontrolEnum $jenisFilter, ?int $bulan = null, ?int $tahun = null)
    {
        // Default value
        $bulan ??= date('m');
        $tahun ??= date('Y');

        // Validate inputs
        $data = compact('bulan', 'tahun', 'noBpjs');
        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf(
            '%s/ListRencanaKontrol/Bulan/%d/Tahun/%d/Nokartu/%s/filter/%d',
            $this->getServiceName(),
            $bulan,
            $tahun,
            $noBpjs,
            $jenisFilter->value,
        );
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($result, $this->transporter->getTimestamp());
    }

    public function list(string $tanggalAwal, string $tanggalAkhir, JenisFilterRencanaKontrolEnum $filter)
    {
        // Validate inputs
        $data = compact('tanggalAwal', 'tanggalAkhir');
        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf(
            '%s/ListRencanaKontrol/tglAwal/%s/tglAkhir/%s/filter/%s',
            $this->getServiceName(),
            $tanggalAwal,
            $tanggalAkhir,
            $filter->value,
        );
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($result, $this->transporter->getTimestamp());
    }

    /**
     * Data spesialistik untuk Rencana Kontrol.
     *
     * @param  JenisKontrolEnum  $jenisKontrol  Jenis Kontrol SPRI / Rencana Kontrol
     * @param  string  $nomor  Jika jenis kontrol SPRI, nomor diisi No BPJS. Jika jenis kontrol Rencana Kontrol, nomor diisi Nomor SEP.
     * @param  string  $tglKontrol  Tanggal rencana Kontrol (Y-m-d)
     * @return mixed
     */
    public function spesialistik(JenisKontrolEnum $jenisKontrol, string $nomor, string $tglKontrol)
    {
        // Validate inputs
        $data = compact('nomor', 'tglKontrol');
        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf(
            '%s/ListSpesialistik/JnsKontrol/%d/nomor/%s/TglRencanaKontrol/%s',
            $this->getServiceName(),
            $jenisKontrol->value,
            $nomor,
            $tglKontrol,
        );
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($result, $this->transporter->getTimestamp());
    }

    /**
     * List dokter untuk rencana kontrol
     *
     * @param  JenisKontrolEnum  $jenisKontrol  Jenis Kontrol Enum. SPRI / Rencana Kontrol
     * @param  string  $kodePoli  Kode Poli dari Data Poli/spesialistik
     * @param  string  $tglKontrol  Tanggal rencana kontrol (Y-m-d)
     * @return mixed
     */
    public function dokter(JenisKontrolEnum $jenisKontrol, string $kodePoli, string $tglKontrol)
    {
        // Validate inputs
        $data = compact('kodePoli', 'tglKontrol');
        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf(
            '%s/JadwalPraktekDokter/JnsKontrol/%d/KdPoli/%s/TglRencanaKontrol/%s',
            $this->getServiceName(),
            $jenisKontrol->value,
            $kodePoli,
            $tglKontrol,
        );
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($result, $this->transporter->getTimestamp());
    }

    /**
     * Get validation rules for the provided keys.
     */
    public function getValidationRules(array $keys): array
    {
        $rules = [
            'noBpjs' => v::stringType()->length(13, 15)->setName('Nomor BPJS'),
            'tglKontrol' => v::date('Y-m-d')
                ->oneOf(
                    v::greaterThan((new DateTime)->format('Y-m-d')),
                    v::equals((new DateTime)->format('Y-m-d'))
                ),
            'noSep' => v::stringType()->length(19, 19, true),
            'bulan' => v::intVal()->between(1, 12),
            'tahun' => v::intVal()->greaterThan(2000),
            'tanggalAwal' => v::date('Y-m-d'),
            'tanggalAkhir' => v::date('Y-m-d'),
        ];

        return array_intersect_key($rules, array_flip($keys));
    }
}
