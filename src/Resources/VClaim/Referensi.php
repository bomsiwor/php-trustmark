<?php

namespace Bomsiwor\Trustmark\Resources\VClaim;

use Bomsiwor\Trustmark\Contracts\DecryptorContract;
use Bomsiwor\Trustmark\Contracts\Resources\VClaimContract;
use Bomsiwor\Trustmark\Core\PackageValidator;
use Bomsiwor\Trustmark\Enums\VClaim\JenisFaskesEnum;
use Bomsiwor\Trustmark\Enums\VClaim\JenisPelayananBPJSEnum;
use Bomsiwor\Trustmark\Responses\VClaimResponse;
use Bomsiwor\Trustmark\Transporters\HttpTransporter;
use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;

final class Referensi extends BaseVClaim implements VClaimContract
{
    private DecryptorContract $decryptor;

    public function __construct(private readonly HttpTransporter $transporter)
    {
        $this->decryptor = $this->createDecryptor($this->transporter->getConfig('consId'), $this->transporter->getConfig('secretKey'));
    }

    public function getServiceName(): string
    {
        return 'referensi';
    }

    /**
     * Pencarian data ICD-10 yang di-support oleh BPJS
     *
     * @param  string  $query  Kode atau nama Diagnosis
     * @return mixed
     */
    public function diagnosis(string $query)
    {
        // Write Format URI
        $formatUri = sprintf('%s/diagnosa/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $query,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data poli.
     *
     * @param  string  $query  Pencarian poli
     * @return mixed
     */
    public function poli(string $query)
    {
        // Write Format URI
        $formatUri = sprintf('%s/poli/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $query,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data fasyankes berdasarkan jenis.
     *
     * @param  string  $query  Nama atau query pencarian fasyankes
     * @param  JenisFaskesEnum  $jenisFaskes  Jenis faskes.
     * @return mixed
     */
    public function faskes(string $query, JenisFaskesEnum $jenisFaskes)
    {
        // Write Format URI
        $formatUri = sprintf('%s/faskes/%s/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $query,
            $jenisFaskes->value,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data DPJP untuk mengisi data DPJPLayan
     *
     * @param  JenisPelayananBPJSEnum  $jenisPelayanan  $jenisPelayanan
     * @param  string  $tglPelayanan  Tanggal pelayanan
     * @param  string  $kodeSpesialis  Kode Spesialis
     * @return mixed
     */
    public function dpjpLayan(JenisPelayananBPJSEnum $jenisPelayanan, string $tglPelayanan, string $kodeSpesialis)
    {
        // Validate Data
        PackageValidator::validate(
            [
                'tglPelayanan' => $tglPelayanan,
            ],
            [
                'tglPelayanan' => 'required|string|date_format:Y-m-d|before_or_equal:today',
            ]
        );

        // Write Format URI
        $formatUri = sprintf('%s/dokter/pelayanan/%s/tglPelayanan/%s/Spesialis/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $jenisPelayanan->value,
            $$tglPelayanan,
            $kodeSpesialis,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data provinsi BPJS.
     *
     * @return mixed
     */
    public function provinsi()
    {
        // Write Format URI
        $formatUri = sprintf('%s/propinsi');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian kabupaten berdasarkan kode Provinsi BPJS.
     * Kode Provinsi nasional tidak dapat digunakan disini.
     *
     * @param  string  $kodeProvinsi  Kode Provinsi.
     * @return mixed
     */
    public function kabupaten(string $kodeProvinsi)
    {
        // Write Format URI
        $formatUri = sprintf('%s/kabupaten/propinsi/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $kodeProvinsi,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data kecamatan berdasarkan kode kabupaten BPJS.
     * Kode kota nasional tidak dapat dipakai.
     *
     * @param  string  $kodeKabupaten  Kode Kabupaten BPJS
     * @return mixed
     */
    public function kecamatan(string $kodeKabupaten)
    {
        // Write Format URI
        $formatUri = sprintf('%s/kecamatan/kabupaten/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $kodeKabupaten,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data diagnosis PRB
     *
     * @return mixed
     */
    public function diagnosisPRB()
    {
        // Write Format URI
        $formatUri = sprintf('%s/diagnosaprb');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data obat generik PRB
     *
     * @param  string  $namaObat  Nama obat generik
     * @return mixed
     */
    public function obatPRB(string $namaObat)
    {
        // Write Format URI
        $formatUri = sprintf('%s/obatprb/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $namaObat,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data procedure tindakan untuk LPK
     *
     * @param  string  $query  Nama atau kode prosedur/tindakan
     * @return mixed
     */
    public function tindakanLPK(string $query)
    {
        // Write Format URI
        $formatUri = sprintf('%s/procedure/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $query,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data kelas rawat untuk LPK
     *
     * @return mixed
     */
    public function kelasRawatLPK()
    {
        // Write Format URI
        $formatUri = sprintf('%s/kelasrawat');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data dokter dalam faskes sesuai cons ID untuk LPK
     *
     * @param  string  $query  Nama dokter
     * @return mixed
     */
    public function dokterLPK(string $query)
    {
        // Write Format URI
        $formatUri = sprintf('%s/dokter/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $query,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencariand ata spesialistik untuk LPK
     *
     * @return mixed
     */
    public function spesialistik()
    {
        // Write Format URI
        $formatUri = sprintf('%s/spesialistik');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data ruang rawat untuk LPK
     *
     * @return mixed
     */
    public function ruangRawat()
    {
        // Write Format URI
        $formatUri = sprintf('%s/ruangrawat');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data cara keluar untuk LPK
     *
     * @return mixed
     */
    public function caraKeluar()
    {
        // Write Format URI
        $formatUri = sprintf('%s/carakeluar');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian kondisi pasca pulang pasien untuk LPK
     *
     * @return mixed
     */
    public function kondisiPascaPulang()
    {
        // Write Format URI
        $formatUri = sprintf('%s/pascapulang');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function getValidationRules(array $keys): array
    {
        return [];
    }
}
