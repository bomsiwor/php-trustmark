<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Resources\VClaim;

use Bomsiwor\Trustmark\Contracts\DecryptorContract;
use Bomsiwor\Trustmark\Contracts\Resources\VClaimContract;
use Bomsiwor\Trustmark\Enums\VClaim\AssesmentPelayananEnum;
use Bomsiwor\Trustmark\Enums\VClaim\FlagProcedureEnum;
use Bomsiwor\Trustmark\Enums\VClaim\JenisFaskesEnum;
use Bomsiwor\Trustmark\Enums\VClaim\JenisKecelakaanBPJSEnum;
use Bomsiwor\Trustmark\Enums\VClaim\JenisPelayananBPJSEnum;
use Bomsiwor\Trustmark\Enums\VClaim\KodePenunjangSEPEnum;
use Bomsiwor\Trustmark\Enums\VClaim\TujuanKunjunganEnum;
use Bomsiwor\Trustmark\Exceptions\VClaimException;
use Bomsiwor\Trustmark\Responses\VClaimResponse;
use Bomsiwor\Trustmark\Transporters\HttpTransporter;
use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;
use DateTime;
use Respect\Validation\Validator as v;

final class SEP extends BaseVClaim implements VClaimContract
{
    private DecryptorContract $decryptor;

    public function __construct(private readonly HttpTransporter $transporter)
    {
        $this->decryptor = $this->createDecryptor($this->transporter->getConfig('consId'), $this->transporter->getConfig('secretKey'));
    }

    public function getServiceName(): string
    {
        return 'SEP';
    }

    /**
     * SEP.
     * Pengambilan data detail nomor SEP
     *
     * @param  string  $noSep  Nomor SEP
     * @return mixed
     */
    public function cari(string $noSep)
    {
        // Validate inputs
        $data = compact('noSep');
        $rules = $this->getValidationRules(['noSep']);
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf('%s/%s', $this->getServiceName(), $noSep);
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * SEP.
     * Melihat data detail SEP Terakhir Peserta Berdasarkan Nomor Rujukan
     *
     * @param  string  $noRujukan  Nomor Rujukan
     * @return mixed
     */
    public function lastSepByNoRujukan(string $noRujukan)
    {
        // Validate inputs
        $data = compact('noRujukan');
        $rules = $this->getValidationRules(['noRujukan']);
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf('%s/Rujukan/lastsep/norujukan/%s', $this->getServiceName(), $noRujukan);
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * FINGERPRINT.
     * Pencarian data atau pengecekan data fingerprint peserta untuk tanggal pelayanan tertentu.
     *
     * @param  string  $noBpjs  Nomor BPJS
     * @param  string|null  $tglPelayanan  Tanggal pelayanan yang dipilih. Default: Hari ini.
     * @return mixed
     */
    public function checkFingerprint(string $noBpjs, ?string $tglPelayanan = null)
    {
        // Set default date if not provided
        $tglPelayanan = $this->getDefaultDate($tglPelayanan);

        // Validate inputs
        $data = compact('noBpjs', 'tglPelayanan');
        $rules = $this->getValidationRules(['noBpjs', 'tglPelayanan']);
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf('%s/FingerPrint/Peserta/%s/TglPelayanan/%s', $this->getServiceName(), $noBpjs, $tglPelayanan);
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * FINGERPRINT.
     * Mendapat list fingerprint pada saat tanggal pelayanan yang dipilih.
     *
     * @param  string|null  $tglPelayanan  Tanggal pelayanan, default: Hari ini.
     * @return mixed
     */
    public function fingerprintList(?string $tglPelayanan = null)
    {
        // Set default date if not provided
        $tglPelayanan = $this->getDefaultDate($tglPelayanan);

        // Validate inputs
        $data = compact('tglPelayanan');
        $rules = $this->getValidationRules(['tglPelayanan']);
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf('%s/FingerPrint/List/Peserta/TglPelayanan/%s', $this->getServiceName(), $tglPelayanan);
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Jasa Raharja.
     * Pencarian data potensi SEP Sebagai Suplesi Jasa Raharja
     *
     * @param  string  $noBpjs  Nomor BPJS Pasien
     * @param  string|null  $tglPelayanan  Tanggal Pelayanan. Default = today
     * @return mixed
     */
    public function suplesiJasaRaharja(string $noBpjs, ?string $tglPelayanan = null)
    {
        // Set default date if not provided
        $tglPelayanan = $this->getDefaultDate($tglPelayanan);

        // Validate inputs
        $data = compact('noBpjs', 'tglPelayanan');
        $rules = $this->getValidationRules(['noBpjs', 'tglPelayanan']);
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf('%s/sep/JasaRaharja/Suplesi/%s/tglPelayanan/%s', $this->getServiceName(), $tglPelayanan);
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Jasa Raharja.
     * Pencarian data SEP Induk Kecelakaan Lalu Lintas
     *
     * @param  string  $noBpjs  Nomor BPJS
     * @return mixed
     */
    public function dataIndukKecelakaan(string $noBpjs)
    {
        // Validate inputs
        $data = compact('noBpjs');
        $rules = $this->getValidationRules(['noBpjs']);
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf('%s/sep/KllInduk/List/%s', $this->getServiceName(), $noBpjs);
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Approval Persetujuan SEP.
     * List Approval Persetujuan SEP
     *
     * @param  int  $bulan  Bulan (1-12)
     * @param  int  $tahun  Tahun. Min : 2000
     * @return mixed
     */
    public function listApproval(int $bulan, int $tahun)
    {
        // Validate inputs
        $data = compact('bulan', 'tahun');
        $rules = $this->getValidationRules(['bulan', 'tahun']);
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf('%s/persetujuanSEP/list/bulan/%d/tahun/%d', $this->getServiceName(), $bulan, $tahun);
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function insert(array $data): mixed
    {
        $rules = $this->getValidationRules(['insertSEP']);
        $this->validate(['insertSEP' => $data], $rules);

        // Construct data based on BPJS API
        $body = $this->createBody('insertSEP', $data);
        // return $body;

        // Create request payload
        $uri = '%s/2.0/insert';
        $payload = Payload::insert($uri, [$this->getServiceName()], content: $body);

        // Send request
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function update(array $data): mixed
    {
        $rules = $this->getValidationRules(['updateSEP']);
        $this->validate(['updateSEP' => $data], $rules);

        // Construct data based on BPJS API
        $body = $this->createBody('updateSEP', $data);

        // Create request payload
        $uri = '%s/2.0/update';
        $payload = Payload::update($uri, [$this->getServiceName()], content: $body);

        // Send request
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Menghapus data SEP menggunakan versi 2.0
     *
     * @param  string  $noSep  Nomor SEP 19 digit
     * @param  string  $user  Nama user
     */
    public function delete(string $noSep, string $user): mixed
    {
        // RUles
        $data = compact('noSep', 'user');
        $rules = $this->getValidationRules(array_keys($data));
        // Validate
        $this->validate($data, $rules);

        // Construct request body based on BPSJ API
        $body = $this->createBody('deleteSEP', $data);

        // Create request payload
        $uri = '%s/2.0/delete';
        $payload = Payload::delete($uri, [$this->getServiceName()], $body);

        // Send request
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp(), false);
    }

    /**
     * UPDATE TANGGAL PULANG SEP.
     * List Update tangal pulang SEP.
     *
     * @param  int  $bulan  Bulan (1-12)
     * @param  int  $tahun  Tahun. Min : 2000
     * @return mixed
     */
    public function listTanggalPulang(int $bulan, int $tahun)
    {
        // Validate inputs
        $data = compact('bulan', 'tahun');
        $rules = $this->getValidationRules(['bulan', 'tahun']);
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf('%s/updtglplg/list/bulan/%d/tahun/%d/', $this->getServiceName(), $bulan, $tahun);
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function updateTglPulang(array $data)
    {
        // Validate inputs
        $rules = $this->getValidationRules(['updateTglPulang']);
        $this->validate(['updateTglPulang' => $data], $rules);

        // Create body
        $body = $this->createBody('updateTglPulang', $data);

        // Create request payload
        $uri = '%s/2.0/updtglplg';
        $payload = Payload::update($uri, [$this->getServiceName()], content: $body);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * INACBG.
     * Pencarian No SEP untuk aplikasi INACBG
     *
     * @param  string  $noSep  Nomor SEP
     * @return mixed
     */
    public function inacbg(string $noSep)
    {
        // Validate inputs
        $data = compact('noSep');
        $rules = $this->getValidationRules(['noSep']);
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf('%s/cbg/%s', $this->getServiceName(), $noSep);
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp(), false);
    }

    /**
     * List SEP Internal
     *
     * @param  string  $noSep  Nomor SEP 19 digit
     * @return mixed
     */
    public function listSEPInternal(string $noSep)
    {
        // Validate inputs
        $data = compact('noSep');
        $rules = $this->getValidationRules(['noSep']);
        $this->validate($data, $rules);

        // Create request payload
        $uri = sprintf('%s/Internal/%s', $this->getServiceName(), $noSep);
        $payload = Payload::get($uri);

        // Send request and handle response
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Get the default date if none is provided.
     */
    private function getDefaultDate(?string $tglPelayanan): string
    {
        return $tglPelayanan ?? (new DateTime())->format('Y-m-d');
    }

    /**
     * Get validation rules for the provided keys.
     */
    public function getValidationRules(array $keys): array
    {
        // SEP SHared Rules
        $sharedRules = [
            'noSep' => v::stringType()->length(19, 19, true),
            'klsRawatHak' => v::intType()->between(1, 3),
            'klsRawatNaik' => v::nullable(v::intType()->between(1, 8)),
            'pembiayaan' => v::nullable(v::intType()->between(1, 3)),
            'penanggungJawab' => v::nullable(v::stringType()),
            'noMR' => v::stringType()->length(5, null),
            'catatan' => v::nullable(v::stringType()),
            'diagAwal' => v::stringType(),
            'poliTujuan' => v::stringType(),
            'poliEksekutif' => v::boolType(),
            'cob' => v::boolType(),
            'katarak' => v::boolType(),
            'jaminanLakaLantas' => v::nullable(v::intType()->in(JenisKecelakaanBPJSEnum::values())),
            'jaminanNoLP' => v::nullable(v::stringType()->length(0, null)),
            'jaminanTglKejadian' => v::nullable(v::stringType()->date('Y-m-d')),
            'jaminanKeterangan' => v::nullable(v::stringType()),
            'jaminanSuplesi' => v::nullable(v::boolType()),
            'jaminanNoSepSuplesi' => v::nullable(v::stringType()->length(10, null)),
            'jaminanLakaProvinsi' => v::nullable(v::stringType()),
            'jaminanLakaKabupaten' => v::nullable(v::stringType()),
            'jaminanLakaKecamatan' => v::nullable(v::stringType()),
            'dpjpLayan' => v::nullable(v::stringType()->length(3, null)),
            'noTelp' => v::nullable(v::stringType()->length(8)),
            'user' => v::stringType()->length(3, null),
            'noBpjs' => v::stringType()->length(13, 15)->setName('Nomor BPJS'),
        ];

        $rules = [
            ...$sharedRules,
            'tglPelayanan' => v::date('Y-m-d')
                ->oneOf(
                    v::lessThan((new DateTime())->format('Y-m-d')),
                    v::equals((new DateTime())->format('Y-m-d'))
                ),
            'bulan' => v::intVal()->between(1, 12),
            'tahun' => v::intVal()->greaterThan(2000),

            // SEP insert Object
            'insertSEP' => v::key('noBpjs', $sharedRules['noBpjs'])
                ->key('tglSep', v::date('Y-m-d'))
                ->key('ppkPelayanan', v::stringType()->length(8, 10))
                ->key('jnsPelayanan', v::intType()->in(JenisPelayananBPJSEnum::values()))
                ->key('asalRujukan', v::nullable(v::intType()->in(JenisFaskesEnum::values())))
                ->key('tglRujukan', v::nullable(v::date('Y-m-d')))
                ->key('noRujukan', v::nullable(v::stringType()->length(8, null)))
                ->key('ppkRujukan', v::nullable(v::stringType()))
                ->key('tujuanKunj', v::nullable(v::intType()->in(TujuanKunjunganEnum::values())))
                ->key('flagProcedure', v::nullable(v::intType()->in(FlagProcedureEnum::values())))
                ->key('kdPenunjang', v::nullable(v::intType()->in(KodePenunjangSEPEnum::values())))
                ->key('assesmentPel', v::nullable(v::intType()->in(AssesmentPelayananEnum::values())))
                ->key('skdpNoSurat', v::nullable(v::stringType()->length(15, null)))
                ->key('skdpKodeDPJP', v::nullable(v::stringType()->length(3, null)))
                ->key('klsRawatHak', $sharedRules['klsRawatHak'])
                ->key('klsRawatNaik', $sharedRules['klsRawatNaik'])
                ->key('pembiayaan', $sharedRules['pembiayaan'])
                ->key('penanggungJawab', $sharedRules['penanggungJawab'])
                ->key('noMR', $sharedRules['noMR'])
                ->key('catatan', $sharedRules['catatan'])
                ->key('diagAwal', $sharedRules['diagAwal'])
                ->key('poliTujuan', $sharedRules['poliTujuan'])
                ->key('poliEksekutif', $sharedRules['poliEksekutif'])
                ->key('cob', $sharedRules['cob'])
                ->key('katarak', $sharedRules['katarak'])
                ->key('jaminanLakaLantas', $sharedRules['jaminanLakaLantas'])
                ->key('jaminanNoLP', $sharedRules['jaminanNoLP'])
                ->key('jaminanTglKejadian', $sharedRules['jaminanTglKejadian'])
                ->key('jaminanKeterangan', $sharedRules['jaminanKeterangan'])
                ->key('jaminanSuplesi', $sharedRules['jaminanSuplesi'])
                ->key('jaminanNoSepSuplesi', $sharedRules['jaminanNoSepSuplesi'])
                ->key('jaminanLakaProvinsi', $sharedRules['jaminanLakaProvinsi'])
                ->key('jaminanLakaKabupaten', $sharedRules['jaminanLakaKabupaten'])
                ->key('jaminanLakaKecamatan', $sharedRules['jaminanLakaKecamatan'])
                ->key('dpjpLayan', $sharedRules['dpjpLayan'])
                ->key('noTelp', $sharedRules['noTelp'])
                ->key('user', $sharedRules['user']),
            'updateSEP' => v::key('noSep', $sharedRules['noSep'])
                ->key('klsRawatHak', $sharedRules['klsRawatHak'])
                ->key('klsRawatNaik', $sharedRules['klsRawatNaik'])
                ->key('pembiayaan', $sharedRules['pembiayaan'])
                ->key('penanggungJawab', $sharedRules['penanggungJawab'])
                ->key('noMR', $sharedRules['noMR'])
                ->key('catatan', $sharedRules['catatan'])
                ->key('diagAwal', $sharedRules['diagAwal'])
                ->key('poliTujuan', $sharedRules['poliTujuan'])
                ->key('poliEksekutif', $sharedRules['poliEksekutif'])
                ->key('cob', $sharedRules['cob'])
                ->key('katarak', $sharedRules['katarak'])
                ->key('jaminanLakaLantas', $sharedRules['jaminanLakaLantas'])
                ->key('jaminanNoLP', $sharedRules['jaminanNoLP'])
                ->key('jaminanTglKejadian', $sharedRules['jaminanTglKejadian'])
                ->key('jaminanKeterangan', $sharedRules['jaminanKeterangan'])
                ->key('jaminanSuplesi', $sharedRules['jaminanSuplesi'])
                ->key('jaminanNoSepSuplesi', $sharedRules['jaminanNoSepSuplesi'])
                ->key('jaminanLakaProvinsi', $sharedRules['jaminanLakaProvinsi'])
                ->key('jaminanLakaKabupaten', $sharedRules['jaminanLakaKabupaten'])
                ->key('jaminanLakaKecamatan', $sharedRules['jaminanLakaKecamatan'])
                ->key('dpjpLayan', $sharedRules['dpjpLayan'])
                ->key('noTelp', $sharedRules['noTelp']),
            'deleteSEP' => v::key('noSep', $sharedRules['noSep'])
                ->key('user', $sharedRules['user']),
            'updateTglPulang' => v::key('noSep', $sharedRules['noSep'])
                ->key('user', $sharedRules['user'])
                ->key('tglPulang', v::date('Y-m-d')),
        ];

        return array_intersect_key($rules, array_flip($keys));
    }

    private function createBody(string $key, mixed $raw): mixed
    {
        $structures = [
            'insertSEP' => fn ($data) => [
                'request' => [
                    't_sep' => [
                        'noKartu' => $data['noBpjs'],
                        'tglSep' => $data['tglSep'],
                        'ppkPelayanan' => $data['ppkPelayanan'],
                        'jnsPelayanan' => $data['jnsPelayanan'],
                        'klsRawat' => [
                            'klsRawatHak' => $data['klsRawatHak'],
                            'klsRawatNaik' => $data['klsRawatNaik'] ?? '',
                            'pembiayaan' => $data['pembiayaan'] ?? '',
                            'penanggungJawab' => $data['penanggungJawab'] ?? '',
                        ],
                        'noMR' => $data['noMR'],
                        'rujukan' => [
                            'asalRujukan' => $data['asalRujukan'] ?? '',
                            'tglRujukan' => $data['tglRujukan'] ?? '',
                            'noRujukan' => $data['noRujukan'] ?? '',
                            'ppkRujukan' => $data['ppkRujukan'] ?? '',
                        ],
                        'catatan' => $data['catatan'],
                        'diagAwal' => $data['diagAwal'],
                        'poli' => [
                            'tujuan' => $data['poliTujuan'],
                            'eksekutif' => convertBooleanToBpjsString($data['poliEksekutif']),
                        ],
                        'cob' => [
                            'cob' => convertBooleanToBpjsString($data['cob']),
                        ],
                        'katarak' => [
                            'katarak' => convertBooleanToBpjsString($data['katarak']),
                        ],
                        'jaminan' => [
                            'lakaLantas' => $data['jaminanLakaLantas'] ?? '',
                            'noLP' => $data['jaminanNoLP'] ?? '',
                            'penjamin' => [
                                'tglKejadian' => $data['jaminanTglKejadian'] ?? '',
                                'keterangan' => $data['jaminanKeterangan'] ?? '',
                                'suplesi' => [
                                    'suplesi' => $data['jaminanSuplesi'] ?? '',
                                    'noSepSuplesi' => $data['jaminanNoSepSuplesi'] ?? '',
                                    'lokasiLaka' => [
                                        'kdPropinsi' => $data['jaminanLakaProvinsi'] ?? '',
                                        'kdKabupaten' => $data['jaminanLakaKabupaten'] ?? '',
                                        'kdKecamatan' => $data['jaminanLakaKecamatan'] ?? '',
                                    ],
                                ],
                            ],
                        ],
                        'tujuanKunj' => $data['tujuanKunj'] ?? '',
                        'flagProcedure' => $data['flagProcedure'] ?? '',
                        'kdPenunjang' => $data['kdPenunjang'] ?? '',
                        'assesmentPel' => $data['assesmentPel'] ?? '',
                        'skdp' => [
                            'noSurat' => $data['skdpNoSurat'] ?? '',
                            'kodeDPJP' => $data['skdpKodeDPJP'] ?? '',
                        ],
                        'dpjpLayan' => $data['dpjpLayan'],
                        'noTelp' => $data['noTelp'],
                        'user' => $data['user'],
                    ],
                ],
            ],
            'updateSEP' => fn ($data) => [
                'request' => [
                    't_sep' => [
                        'noSep' => $data['noSep'],
                        'klsRawat' => [
                            'klsRawatHak' => strval($data['klsRawatHak']),
                            'klsRawatNaik' => $data['klsRawatNaik'] ?? '',
                            'pembiayaan' => $data['pembiayaan'] ?? '',
                            'penanggungJawab' => $data['penanggungJawab'] ?? '',
                        ],
                        'noMR' => $data['noMR'],
                        'catatan' => $data['catatan'],
                        'diagAwal' => $data['diagAwal'],
                        'poli' => [
                            'tujuan' => $data['poliTujuan'],
                            'eksekutif' => convertBooleanToBpjsString($data['poliEksekutif']),
                        ],
                        'cob' => [
                            'cob' => convertBooleanToBpjsString($data['cob']),
                        ],
                        'katarak' => [
                            'katarak' => convertBooleanToBpjsString($data['katarak']),
                        ],
                        'jaminan' => [
                            'lakaLantas' => ! is_null($data['jaminanLakaLantas']) ? strval($data['jaminanLakaLantas']) : '',
                            'penjamin' => [
                                'tglKejadian' => $data['jaminanTglKejadian'] ?? '',
                                'keterangan' => $data['jaminanKeterangan'] ?? '',
                                'suplesi' => [
                                    'suplesi' => convertBooleanToBpjsString($data['jaminanSuplesi']),
                                    'noSepSuplesi' => $data['jaminanNoSepSuplesi'] ?? '',
                                    'lokasiLaka' => [
                                        'kdPropinsi' => $data['jaminanLakaProvinsi'] ?? '',
                                        'kdKabupaten' => $data['jaminanLakaKabupaten'] ?? '',
                                        'kdKecamatan' => $data['jaminanLakaKecamatan'] ?? '',
                                    ],
                                ],
                            ],
                        ],
                        'dpjpLayan' => $data['dpjpLayan'],
                        'noTelp' => $data['noTelp'],
                        'user' => $data['user'],
                    ],
                ],
            ],
            'deleteSEP' => fn ($data) => [
                'request' => [
                    't_sep' => [
                        'noSep' => $data['noSep'],
                        'user' => $data['user'],
                    ],
                ],
            ],
            'updateTglPulang' => fn ($data) => [
                'request' => [
                    't_sep' => [
                        'noSep' => $data['noSep'],
                        'statusPulang' => $data['statusPulang'],
                        'noSuratMeninggal' => $data['noSuratMeninggal'],
                        'tglMeninggal' => $data['tglMeninggal'],
                        'tglPulang' => $data['tglPulang'],
                        'noLPManual' => $data['noLPManual'],
                        'user' => $data['user'],
                    ],
                ],
            ],
        ];

        // Throw error if key not exists
        if (! array_key_exists($key, $structures)) {
            throw new VClaimException("Key {$key} not exists on structures", 'Validation');
        }

        return $structures[$key]($raw);
    }
}
