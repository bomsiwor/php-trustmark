<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Resources\VClaim;

use Bomsiwor\Trustmark\Contracts\DecryptorContract;
use Bomsiwor\Trustmark\Contracts\Resources\VClaimContract;
use Bomsiwor\Trustmark\Enums\VClaim\JenisFilterRencanaKontrol;
use Bomsiwor\Trustmark\Enums\VClaim\JenisFilterRencanaKontrolEnum;
use Bomsiwor\Trustmark\Enums\VClaim\JenisKontrolEnum;
use Bomsiwor\Trustmark\Exceptions\TrustmarkException;
use Bomsiwor\Trustmark\Responses\VClaimResponse;
use Bomsiwor\Trustmark\Transporters\HttpTransporter;
use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;
use Respect\Validation\Validator as v;

final class RencanaKontrol extends BaseVClaim implements VClaimContract
{
    public function __construct(private readonly HttpTransporter $transporter, private readonly DecryptorContract $decryptor) {}

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

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
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

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
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

        // Compute Value
        $bulan = str_pad(strval($bulan), 2, '0', STR_PAD_LEFT);

        // Validate inputs
        $data = compact('bulan', 'tahun', 'noBpjs');
        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf(
            '%s/ListRencanaKontrol/Bulan/%s/Tahun/%d/Nokartu/%s/filter/%d',
            $this->getServiceName(),
            $bulan,
            $tahun,
            $noBpjs,
            $jenisFilter->value,
        );

        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Mendapatkan data rencana kontrol yang sudah diterbitkan oleh faskes.
     *
     * @param  string  $tanggalAwal  Tanggal awal filter (format : Y-m-d)
     * @param  string  $tanggalAkhir  Tanggal akhir filter (format : Y-m-d)
     * @param  JenisFilterRencanaKontrolEnum  $filter  Enum jenis data yang akan dicari (entry atau rencana)
     * @return mixed
     */
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

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Buat data rencana kontrol baru
     *
     * @param  array  $data  Data rencana kontrol
     */
    public function insertRencanaKontrol(array $data): mixed
    {
        $rules = $this->getValidationRules(['insertRencanaKontrol']);
        $this->validate(['insertRencanaKontrol' => $data], $rules);

        // Construct data based on BPJS API
        $body = $this->createBody('insertRencanaKontrol', $data);

        // Creat erequest payload
        $uri = '%s/insert';
        $payload = Payload::insert($uri, [$this->getServiceName()], $body);

        // Send request and get result
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Update surat kontrol yang sudah dibuat.
     *
     * @param  array  $data  $data
     */
    public function updateRencanaKontrol(array $data): mixed
    {
        $rules = $this->getValidationRules(['updateRencanaKontrol']);

        $this->validate(['updateRencanaKontrol' => $data], $rules);

        // Construct data based on BPJS API
        $body = $this->createBody('updateRencanaKontrol', $data);

        // Creat erequest payload
        $uri = '%s/Update';
        $payload = Payload::update($uri, [$this->getServiceName()], $body);

        // Send request and get result
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Membuat data SPRI baru
     *
     * @param  array  $data  Data untuk SPRI
     */
    public function insertSPRI(array $data): mixed
    {
        $rules = $this->getValidationRules(['insertSPRI']);
        $this->validate(['insertSPRI' => $data], $rules);

        // Construct data based on BPJS API
        $body = $this->createBody('insertSPRI', $data);

        // Creat erequest payload
        $uri = '%s/InsertSPRI';
        $payload = Payload::insert($uri, [$this->getServiceName()], $body);

        // Send request and get result
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Update SPRI yang sudah dibuat sebelunya
     *
     * @param  array  $data  Data untuk SPRI
     */
    public function updateSPRI(array $data): mixed
    {
        $rules = $this->getValidationRules(['updateSPRI']);
        $this->validate(['updateSPRI' => $data], $rules);

        // Construct data based on BPJS API
        $body = $this->createBody('updateSPRI', $data);

        // Creat erequest payload
        $uri = '%s/UpdateSPRI';
        $payload = Payload::update($uri, [$this->getServiceName()], $body);

        // Send request and get result
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Hapus rencana kontrol yangs sudah dibuat sebelumnya
     *
     * @param  array  $data  Data untuk hapus rencana kontrol
     */
    public function deleteRencanaKontrol(array $data): mixed
    {
        $rules = $this->getValidationRules(['deleteRencanaKontrol']);

        $this->validate(['deleteRencanaKontrol' => $data], $rules);

        // Construct data based on BPJS API
        $body = $this->createBody('deleteRencanaKontrol', $data);

        // Creat erequest payload
        $uri = '%s/Delete';
        $payload = Payload::delete($uri, [$this->getServiceName()], $body);

        // Send request and get result
        $result = $this->transporter->sendRequest($payload);

        return $result;

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
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
        $data = [
            'noSEP' => $nomor,
            'tglRencanaKontrol' => $tglKontrol,
        ];

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

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
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
        $data = [
            'tglRencanaKontrol' => $tglKontrol,
        ];

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

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Get validation rules for the provided keys.
     */
    public function getValidationRules(array $keys): array
    {

        $sharedRules = [
            'noSEP' => v::stringType()->length(19, 19, true),
            'tglRencanaKontrol' => v::stringType()->date('Y-m-d'),
            'kodeDokter' => v::nullable(v::stringType()->length(3, null)),
            'noSuratKontrol' => v::stringType()->length(15, null),
            'user' => v::stringType()->length(3, null),
            'poliKontrol' => v::stringType(),
        ];

        $rules = [
            ...$sharedRules,
            'insertRencanaKontrol' => v::key('noSEP', $sharedRules['noSEP'])
                ->key('kodeDokter', $sharedRules['kodeDokter'])
                ->key('poliKontrol', $sharedRules['poliKontrol'])
                ->key('tglRencanaKontrol', $sharedRules['tglRencanaKontrol'])
                ->key('user', $sharedRules['user']),
            'updateRencanaKontrol' => v::key('noSEP', $sharedRules['noSEP'])
                ->key('kodeDokter', $sharedRules['kodeDokter'])
                ->key('poliKontrol', $sharedRules['poliKontrol'])
                ->key('tglRencanaKontrol', $sharedRules['tglRencanaKontrol'])
                ->key('noSuratKontrol', $sharedRules['noSuratKontrol'])
                ->key('user', $sharedRules['user']),
            'insertSPRI' => v::key('noSEP', $sharedRules['noSEP'])
                ->key('kodeDokter', $sharedRules['kodeDokter'])
                ->key('poliKontrol', $sharedRules['poliKontrol'])
                ->key('tglRencanaKontrol', $sharedRules['tglRencanaKontrol'])
                ->key('user', $sharedRules['user']),
            'updateSPRI' => v::key('noSEP', $sharedRules['noSEP'])
                ->key('kodeDokter', $sharedRules['kodeDokter'])
                ->key('poliKontrol', $sharedRules['poliKontrol'])
                ->key('tglRencanaKontrol', $sharedRules['tglRencanaKontrol'])
                ->key('user', $sharedRules['user'])
                ->key('noSPRI', v::stringType()->length(15, null)),
            'deleteRencanaKontrol' => v::key('noSuratKontrol', $sharedRules['noSuratKontrol'])
                ->key('user', $sharedRules['user']),
        ];

        return array_intersect_key($rules, array_flip($keys));
    }

    public function createBody(string $key, mixed $raw): mixed
    {
        $structures = [
            'insertRencanaKontrol' => fn ($data) => [
                'request' => [
                    'noSEP' => $data['noSEP'],
                    'kodeDokter' => $data['kodeDokter'],
                    'poliKontrol' => $data['poliKontrol'],
                    'tglRencanaKontrol' => $data['tglRencanaKontrol'],
                    'user' => $data['user'],
                ],
            ],
            'updateRencanaKontrol' => fn ($data) => [
                'request' => [
                    'noSuratKontrol' => $data['noSuratKontrol'],
                    'noSEP' => $data['noSEP'],
                    'kodeDokter' => $data['kodeDokter'],
                    'poliKontrol' => $data['poliKontrol'],
                    'tglRencanaKontrol' => $data['tglRencanaKontrol'],
                    'user' => $data['user'],
                ],
            ],
            'deleteRencanaKontrol' => fn ($data) => [
                'request' => [
                    't_suratkontrol' => [
                        'noSuratKontrol' => $data['noSuratKontrol'],
                        'user' => $data['user'],
                    ],
                ],
            ],
            'insertSPRI' => fn ($data) => [
                'request' => [
                    'noSEP' => $data['noSEP'],
                    'kodeDokter' => $data['kodeDokter'],
                    'poliKontrol' => $data['poliKontrol'],
                    'tglRencanaKontrol' => $data['tglRencanaKontrol'],
                    'user' => $data['user'],
                ],
            ],
            'updateSPRI' => fn ($data) => [
                'request' => [
                    'noSEP' => $data['noSEP'],
                    'kodeDokter' => $data['kodeDokter'],
                    'poliKontrol' => $data['poliKontrol'],
                    'tglRencanaKontrol' => $data['tglRencanaKontrol'],
                    'noSPRI' => $data['noSPRI'],
                    'user' => $data['user'],
                ],
            ],
        ];

        // Throw error if key not exists
        if (! array_key_exists($key, $structures)) {
            throw new TrustmarkException("Key {$key} not exists on structures", 'Validation');
        }

        return $structures[$key]($raw);
    }
}
