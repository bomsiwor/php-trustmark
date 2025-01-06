<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Resources\VClaim;

use Bomsiwor\Trustmark\Contracts\DecryptorContract;
use Bomsiwor\Trustmark\Contracts\Resources\VClaimContract;
use Bomsiwor\Trustmark\Enums\VClaim\JenisFaskesEnum;
use Bomsiwor\Trustmark\Responses\VClaimResponse;
use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;
use Bomsiwor\Trustmark\Transporters\HttpTransporter;
use DateTime;
use Respect\Validation\Validator as v;

final class Rujukan extends BaseVClaim implements VClaimContract
{
    private DecryptorContract $decryptor;

    public function __construct(private readonly HttpTransporter $transporter)
    {
        $this->decryptor = $this->createDecryptor($this->transporter->getConfig('consId'), $this->transporter->getConfig('secretKey'));
    }

    public function getServiceName(): string
    {
        return 'Rujukan';
    }

    /**
     * Fungsi : Pencarian data rujukan dari Pcare berdasarkan nomor rujukan
     *
     * @param  string  $noRujukan  Nomor Rujukan.
     * @param  JenisFaskesEnum  $jenisFaskes  Enum Jenis Faskes
     * @return mixed
     */
    public function cari(string $noRujukan, JenisFaskesEnum $jenisFaskes)
    {
        // Validate inputs
        $data = [
            'noRujukan' => $noRujukan,
        ];

        $rules = $this->getValidationRules(['noRujukan']);
        $this->validate($data, $rules);

        // Create request payload
        // Conditional for RS and FKTP
        switch ($jenisFaskes) {
            case JenisFaskesEnum::RS:
                $uri = sprintf('%s/RS/%s', $this->getServiceName(), $noRujukan);
                break;

            case JenisFaskesEnum::FKTP:
                $uri = sprintf('%s/%s', $this->getServiceName(), $noRujukan);
                break;
        }

        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data rujukan berdasarkan nomor kartu BPJS Peserta.
     * Akan mendapatkan satu data.
     *
     * @param  string  $noBpjs  Nomor BPJS Peserta.
     * @param  JenisFaskesEnum  $jenisFaskes  Jenis Faskes Enum.
     * @return mixed
     */
    public function cariByNoBPJS(string $noBpjs, JenisFaskesEnum $jenisFaskes)
    {
        // Validate inputs
        $data = [
            'noBpjs' => $noBpjs,
        ];

        $rules = $this->getValidationRules(['noBpjs']);
        $this->validate($data, $rules);

        // Create request payload
        // Conditional for RS and FKTP
        switch ($jenisFaskes) {
            case JenisFaskesEnum::RS:
                $uri = sprintf('%s/RS/Peserta/%s', $this->getServiceName(), $noBpjs);
                break;

            case JenisFaskesEnum::FKTP:
                $uri = sprintf('%s/Peserta/%s', $this->getServiceName(), $noBpjs);
                break;
        }

        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data rujukan berdasarkan data No BPJS Peserta.
     * Hasil berupa array dari banyak data rujukan.
     *
     * @param  string  $noBpjs  Nomor BPJS.
     * @param  JenisFaskesEnum  $jenisFaskes  Jenis Faskes Enum.
     * @return mixed
     */
    public function cariMultiByNoBPJS(string $noBpjs, JenisFaskesEnum $jenisFaskes)
    {
        // Validate inputs
        $data = [
            'noBpjs' => $noBpjs,
        ];

        $rules = $this->getValidationRules(['noBpjs']);
        $this->validate($data, $rules);

        // Create request payload
        // Conditional for RS and FKTP
        switch ($jenisFaskes) {
            case JenisFaskesEnum::RS:
                $uri = sprintf('%s/RS/Peserta/%s', $this->getServiceName(), $noBpjs);
                break;

            case JenisFaskesEnum::FKTP:
                $uri = sprintf('%s/Peserta/%s', $this->getServiceName(), $noBpjs);
                break;
        }

        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Daftar data rujukan khusus.
     *
     * @param  int  $bulan  Bulan (0-12)
     * @param  int  $tahun  Tahun. Minimum 2000
     * @return mixed
     */
    public function listRujukanKhusus(int $bulan, int $tahun)
    {
        // Validate inputs
        $data = compact('bulan', 'tahun');

        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        $uri = sprintf('%s/Khusus/List/Bulan/%d/Tahun/%d', $this->getServiceName(), $bulan, $tahun);

        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Data spesialistik PPK tertentu untuk tanggal tertentu.
     *
     * @param  string  $kodePPK  Kode PPK 8 digit
     * @param  string  $tglRujukan  Tanggal Rujukan (Y-m-d)
     * @return mixed
     */
    public function spesialistik(string $kodePPK, string $tglRujukan)
    {
        // Validate inputs
        $data = compact('kodePPK', 'tglRujukan');

        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        $uri = sprintf('%s/ListSpesialistik/PPKRujukan/%s/TglRujukan/%s', $this->getServiceName(), $kodePPK, $tglRujukan);

        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Daftar data sarana PPK Tujuan Rujuk
     *
     * @param  string  $kodePPK  Kode PPK 8 digit
     * @return mixed
     */
    public function sarana(string $kodePPK)
    {
        // Validate inputs
        $data = compact('kodePPK');

        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        $uri = sprintf('%s/ListSarana/PPKRujukan/%s', $this->getServiceName(), $kodePPK);

        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * List Rujukan Keluar RS
     *
     * @param  string  $tanggalAwal  Tanggal awal (Y-m-d)
     * @param  string  $tanggalAkhir  Tanggal akhir (Y-m-d)
     * @return mixed
     */
    public function listRujukanKeluar(string $tanggalAwal, string $tanggalAkhir)
    {
        // Validate inputs
        $data = compact('tanggalAwal', 'tanggalAkhir');

        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        $uri = sprintf('%s/Keluar/List/tglMulai/%s/tglAkhir/%s', $this->getServiceName(), $tanggalAwal, $tanggalAkhir);

        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Detail Rujukan Keluar RS berdasarkan nomor rujukan
     *
     * @param  string  $noRujukan  Nomor Rujukan
     * @return mixed
     */
    public function detailKeluarRSByNoRujuk(string $noRujukan)
    {
        // Validate inputs
        $data = compact('noRujukan');

        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        $uri = sprintf('%s/Keluar/%s', $this->getServiceName(), $noRujukan);

        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Data jumlah SEP yang terbentu berdasarkan nomor rujukan yang masuk ke RS
     *
     * @param  string  $noRujukan  Nomor Rujukan
     * @param JenisFaskesEnum Jenis Faskes Rujukan Enum
     * @return mixed
     */
    public function jumlahSep(string $noRujukan, JenisFaskesEnum $jenisRujukan)
    {
        // Validate inputs
        $data = compact('noRujukan');

        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        $uri = sprintf('%s/JumlahSEP/%s/%s', $this->getServiceName(), $noRujukan, $jenisRujukan->value);

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
        $rules = [
            'noRujukan' => v::stringType()->length(15, 20)->setName('Nomor Rujukan (noRujukan)'),
            'noBpjs' => v::stringType()->length(13, 15)->setName('Nomor BPJS'),
            'kodePPK' => v::stringType()->length(8, 8)->setName('Kode PPK'),
            'tglPelayanan' => v::date('Y-m-d')
                ->oneOf(
                    v::lessThan((new DateTime())->format('Y-m-d')),
                    v::equals((new DateTime())->format('Y-m-d'))
                ),
            'tanggalAwal' => v::date('Y-m-d'),
            'tanggalAkhir' => v::date('Y-m-d'),
            'noSep' => v::stringType()->length(19, 19, true),
            'bulan' => v::intVal()->between(1, 12),
            'tahun' => v::intVal()->greaterThan(2000),
        ];

        return array_intersect_key($rules, array_flip($keys));
    }
}
