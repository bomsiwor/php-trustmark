<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Resources\Antrean;

use Bomsiwor\Trustmark\Contracts\DecryptorContract;
use Bomsiwor\Trustmark\Contracts\Resources\AntreanContract;
use Bomsiwor\Trustmark\Contracts\TransporterContract;
use Bomsiwor\Trustmark\Responses\VClaimResponse;
use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;
use DateTime;
use InvalidArgumentException;
use Respect\Validation\Validator as v;

final class AntreanBPJS extends BaseWSAntrean implements AntreanContract
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

    /**
     * Mengambil data poli untuk antrean
     *
     * @return mixed Data poli untuk antrean
     */
    public function poli()
    {
        // Write Format URI
        $formatUri = '%s/poli';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName('ref')]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Mengambil data dokter yang terdaftar sesuai faskes.
     *
     * @return mixed Data dokter
     */
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

    /**
     * Mendapatkan data jadwal dokter sesuai tanggal yang dipilih.
     *
     * @param  string  $kodePoli  Kode poli dari referensi poli antrean.
     * @param  string  $tanggal  Tanggal yang dipilih (format: Y-m-d)
     * @return mixed
     */
    public function jadwalDokter(string $kodePoli, string $tanggal)
    {
        // Validate payload
        $data = compact('kodePoli', 'tanggal');
        $rules = $this->getValidationRules(array_keys($data));

        $this->validate($data, $rules);

        // Write Format URI
        $formatUri = 'jadwaldokter/kodepoli/%s/tanggal/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$kodePoli, $tanggal]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Mendapatkan data poli yang valid untuk fingerprint
     *
     * @return mixed
     */
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

    /**
     * Mendapatkan status fingerprint pasien.
     *
     * @param  string  $tipeIdentitas  Tipe identitas (noka | nik)
     * @param  string  $noidentitas  Nomor identitas sesuai dengan tipe identitas yang dipilih
     * @return mixed
     */
    public function pasienFingerprint(string $tipeIdentitas, string $noIdentitas)
    {
        // Validate payload
        $data = compact('tipeIdentitas', 'noIdentitas');
        $rules = $this->getValidationRules(array_keys($data));

        $this->validate($data, $rules);

        // Write Format URI
        $formatUri = '%s/pasien/fp/identitas/%s/noidentitas/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName('ref'), $tipeIdentitas, $noIdentitas]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Update jadwal dokter pada aplikasi HFIS
     *
     * @param  array  $data  Data request
     * @return mixed
     */
    public function updateJadwalDokter(array $data)
    {
        $key = 'updateJadwalDokter';

        // Validate
        $rules = $this->getValidationRules([$key]);
        $this->validate([$key => $data], $rules);

        // Create body
        $body = $this->createBody($key, $data);

        // Write Format URI
        $formatUri = 'jadwaldokter/updatejadwaldokter';

        // Create payload to generate request instance
        $payload = Payload::insert($formatUri, [$this->getServiceName()], $body);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function tambah(array $data)
    {
        // Write Format URI
        $formatUri = '%s/add';

        // Create body
        $body = [];

        // Create payload to generate request instance
        $payload = Payload::insert($formatUri, [$this->getServiceName()], $body);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function updateWaktu(array $data)
    {
        // Write Format URI
        $formatUri = '%s/updatewaktu';

        // Create body
        $body = [];

        // Create payload to generate request instance
        $payload = Payload::insert($formatUri, [$this->getServiceName()], $body);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function tambahAntreanFarmasi(array $data)
    {
        // Write Format URI
        $formatUri = '%s/farmasi/add';

        // Create body
        $body = [];

        // Create payload to generate request instance
        $payload = Payload::insert($formatUri, [$this->getServiceName()], $body);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function batalAntrean(array $data)
    {
        $key = 'batalAntrean';

        // Write Format URI
        $formatUri = '%s/batal';

        // Validate
        $rules = $this->getValidationRules([$key]);
        $this->validate([$key => $data], $rules);

        // Create body
        $body = $this->createBody($key, $data);

        // Create payload to generate request instance
        $payload = Payload::insert($formatUri, [$this->getServiceName()], $body);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Mendapatkan semua task ID untuk kode booking.
     * *
     * @param  string  $kodeBooking  Kode Booking
     * @return mixed
     */
    public function taskIDList(string $kodeBooking)
    {
        // Write Format URI
        $formatUri = '%s/getlisttask';

        // Create payload to generate request instance
        $payload = Payload::insert($formatUri, [$this->getServiceName()], [
            'kodebooking' => $kodeBooking,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Dasboard per tanggal
     *
     * @param  string  $tanggal  Tanggal (format : Y-m-d)
     * @param  string  $waktu  Waktu
     * @return mixed
     */
    public function dashboardPerDate(string $tanggal, string $waktu)
    {

        // Validate payload
        $data = compact('tanggal');
        $rules = $this->getValidationRules(array_keys($data));

        $this->validate($data, $rules);

        // Write Format URI
        $formatUri = '%s/waktutunggu/tanggal/%s/waktu/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName('dash'), $tanggal, $waktu]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return $result;

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp(), false);
    }

    /**
     * Dashboard per bulan
     *
     * @param  int  $bulan  Bulan (1-12)
     * @param  int  $tahun  Tahun (min tahun 2000)
     * @param  string  $waktu  Waktu
     * @return mixed
     */
    public function dashboardPerBulan(int $bulan, int $tahun, string $waktu)
    {

        // Validate payload
        $data = compact('bulan', 'tahun');
        $rules = $this->getValidationRules(array_keys($data));

        $this->validate($data, $rules);

        // Write Format URI
        $formatUri = '%s/waktutunggu/bulan/%s/tahun/%d/waktu/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName('dash'), $bulan, $tahun, $waktu]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Data task list per tanggal
     *
     * @param  string  $tanggal  Tanggal (format : Y-m-d). Default hari ini.
     * @return mixed
     */
    public function perTanggal(?string $tanggal = null)
    {
        // Default value
        $tanggal ??= (new DateTime)->format('Y-m-d');

        // Validate payload
        $data = compact('tanggal');
        $rules = $this->getValidationRules(array_keys($data));

        $this->validate($data, $rules);

        // Write Format URI
        $formatUri = '%s/pendaftaran/tanggal/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName(), $tanggal]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Mendapatkan data task list by kode booking
     *
     * @param  string  $kodeBooking  Kode Booking
     * @return mixed
     */
    public function perKodeBooking(string $kodeBooking)
    {
        // Validate payload
        $data = compact('kodeBooking');
        $rules = $this->getValidationRules(array_keys($data));

        $this->validate($data, $rules);

        // Write Format URI
        $formatUri = '%s/pendaftaran/kodebooking/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName(), $kodeBooking]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Mendapatkan data antrean belum dilayani oleh BPJS.
     *
     * @return mixed
     */
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

    /**
     * Mendapatkan antrean belum dilayani oleh poli dan dokter, serta jam praktik.
     *
     * @param  string  $kodePoli  Kode Poli.
     * @param  string  $kodeDokter  Kode dokter.
     * @param  int  $hari  Hari (1-7)
     * @param  string  $jamPraktik  Jam Praktik.
     * @return mixed
     */
    public function belumDilayaniPerPoli(string $kodePoli, string $kodeDokter, int $hari, string $jamPraktik)
    {
        // Validate payload
        $data = compact('hari', 'kodePoli', 'kodeDokter');

        $rules = $this->getValidationRules(array_keys($data));

        $this->validate($data, $rules);

        // Write Format URI
        $formatUri = '%s/pendaftaran/kodepoli/%s/kodedokter/%s/hari/%d/jampraktek/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName(), $kodePoli, $kodeDokter, $hari, $jamPraktik]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function getValidationRules(array $keys): array
    {
        $sharedRules = $this->getSharedRules();

        $rules = [
            ...$sharedRules,
            'batalAntrean' => v::key('kodeBooking', $sharedRules['kodeBooking'])
                ->key('keterangan', $sharedRules['keterangan']),

            'updateJadwalDokter' => v::key('kodePoli', $sharedRules['kodePoli'])
                ->key('kodeSubspesialis', $sharedRules['kodeSubspesialis'])
                ->key('kodeDokter', $sharedRules['kodeDokter'])
                ->key('jadwal', v::arrayType()->when(
                    v::arrayType()->length(1, null),
                    v::each(
                        v::arrayType()->keySet(
                            v::key('hari', v::intVal()->between(1, 8)),
                            v::key('buka', v::dateTime('H:i')),
                            v::key('tutup', v::dateTime('H:i')),
                        )
                    )
                )),

            'sepDate' => v::date('Y-m-d')
                ->oneOf(
                    v::lessThan((new DateTime)->format('Y-m-d')),
                    v::equals((new DateTime)->format('Y-m-d'))
                ),
        ];

        return array_intersect_key($rules, array_flip($keys));
    }

    public function createBody(string $key, mixed $data): mixed
    {
        $builder = [
            'batalAntrean' => fn ($raw) => [
                'kodebooking' => $raw['kodeBooking'],
                'keterangan' => $raw['keterangan'] ?? '',
            ],

            'updateJadwalDokter' => fn ($raw) => [
                'kodepoli' => $raw['kodePoli'],
                'kodesubspesialis' => $raw['kodeSubspesialis'],
                'kodedokter' => $raw['kodeDokter'],
                'jadwal' => $raw['jadwal'],
            ],
        ];

        return $builder[$key]($data) ?? throw new InvalidArgumentException('Builder key invalid');
    }
}
