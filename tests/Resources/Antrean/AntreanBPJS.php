<?php

use Bomsiwor\Trustmark\Enums\Antrean\JenisKunjunganAntreanEnum;
use Bomsiwor\Trustmark\Enums\Antrean\JenisPasienAntreanEnum;
use Bomsiwor\Trustmark\Enums\Antrean\JenisResepEnum;

use function Pest\Faker\fake;

pest()->group('antrean.bpjs');

test('update jadwal dokter', function () {
    $client = mockAntreanClient(baseAntreanResponse([]));

    // Request data
    $jadwal = [

        [
            'hari' => 1,
            'buka' => '08:00',
            'tutup' => '12:00',
        ],
        [
            'hari' => 1,
            'buka' => '08:00',
            'tutup' => '12:00',
        ],
    ];

    $emptyJadwal = [];

    $requestNormal = [
        'kodePoli' => 'ANA',
        'kodeSubspesialis' => 'ANA',
        'kodeDokter' => '123456',
        'jadwal' => $jadwal,
    ];

    $requestEmptyJadwal = [
        'kodePoli' => 'ANA',
        'kodeSubspesialis' => 'ANA',
        'kodeDokter' => '123456',
        'jadwal' => $emptyJadwal,
    ];

    // Subservice
    $service = $client->bpjs();

    // Create body
    // Assest regular request
    $body = $service->createBody('updateJadwalDokter', $requestNormal);

    expect($body)
        ->toBeArray()
        ->toHaveKey('kodepoli', $requestNormal['kodePoli'])
        ->toHaveKey('kodesubspesialis', $requestNormal['kodeSubspesialis'])
        ->toHaveKey('kodedokter', $requestNormal['kodeDokter'])
        ->and($body['jadwal'])
        ->toBeArray()
        ->each(function ($jadwal) {
            $jadwal->toBeArray()
                ->hari->toBeInt()
                ->buka->toBeString()
                ->tutup->toBeString();
        });

    // Assest regular request
    $body = $service->createBody('updateJadwalDokter', $requestEmptyJadwal);

    expect($body)
        ->toBeArray()
        ->toHaveKey('kodepoli', $requestEmptyJadwal['kodePoli'])
        ->toHaveKey('kodesubspesialis', $requestEmptyJadwal['kodeSubspesialis'])
        ->toHaveKey('kodedokter', $requestEmptyJadwal['kodeDokter'])
        ->and($body['jadwal'])
        ->toBeArray()
        ->toBeEmpty();

    // Create dummy response
    $client->bpjs()->updateJadwalDokter($requestNormal);
});

test('batal antrean', function () {
    $client = mockAntreanClient(baseAntreanResponse([]));

    // Request data
    $kodeBooking = fake()->regexify('[A-Z0-9]{16}');

    $request = [
        'kodeBooking' => $kodeBooking,
        'keterangan' => 'Keterangan here',
    ];

    // Subservice
    $service = $client->bpjs();

    // Create body
    // Assest regular request
    $body = $service->createBody('batalAntrean', $request);

    expect($body)
        ->toBeArray()
        ->toHaveKey('kodebooking', $kodeBooking)
        ->toHaveKey('keterangan', $request['keterangan']);

    // Create dummy response
    $client->bpjs()->batalAntrean($request);
});

test('tambah antrean farmasi', function () {
    $client = mockAntreanClient([]);

    // Request data
    $kodeBooking = fake()->regexify('[A-Z0-9]{16}');

    $request = [
        'kodeBooking' => $kodeBooking,
        'jenisResep' => JenisResepEnum::RACIKAN->value,
        'noAntrean' => 1,
        'keterangan' => 'Keterangan here',
    ];

    // Subservice
    $service = $client->bpjs();

    // Create body
    // Assest regular request
    $body = $service->createBody('tambahAntreanFarmasi', $request);

    expect($body)
        ->toBeArray()
        ->toHaveKey('kodebooking', $kodeBooking)
        ->toHaveKey('jenisresep', 'racikan')
        ->toHaveKey('nomorantrean', 1)
        ->toHaveKey('keterangan', $request['keterangan']);

    // Create dummy response
    $client->bpjs()->tambahAntreanFarmasi($request);
})->throwsNoExceptions()->skip();

test('tambah antrean', function () {
    $client = mockAntreanClient([]);

    // Request data
    $kodeBooking = fake()->regexify('[A-Z0-9]{16}');
    $noBpjs = fake()->regexify('[A-Z0-9]{11}');
    $nik = fake()->regexify('[A-Z0-9]{16}');
    $noHP = fake()->phoneNumber();
    $noRM = fake()->regexify('[A-Z0-9]{6}');
    $kodeDokter = fake()->regexify('[A-Z0-9]{6}');
    $namaDokter = fake()->name();
    $nomorReferensi = fake()->regexify('[A-Z0-9]{19}');

    $request = [
        'kodeBooking' => $kodeBooking,
        'jenisPasien' => JenisPasienAntreanEnum::JKN->value,
        'noBpjs' => $noBpjs,
        'nik' => $nik,
        'noHP' => $noHP,
        'kodePoli' => 'ANA',
        'namaPoli' => 'Poli Anak',
        'pasienBaru' => true,
        'noRM' => $noRM,
        'tanggalPeriksa' => '2025-01-01',
        'kodeDokter' => $kodeDokter,
        'namaDokter' => $namaDokter,
        'jamPraktik' => '08:00-12:00',
        'jenisKunjungan' => JenisKunjunganAntreanEnum::RUJUK_INTERNAL->value,
        'noReferensi' => $nomorReferensi,
        'nomorAntrean' => 'A-12',
        'angkaAntrean' => 12,
        'estimasiDilayani' => 1615869169000,
        'sisaKuotaJKN' => 5,
        'kuotaJKN' => 30,
        'sisaKuotaNonJKN' => 5,
        'kuotaNonJKN' => 50,
        'keterangan' => 'Keterangan here',
    ];

    // Subservice
    $service = $client->bpjs();

    // Create body
    // Assest regular request
    $body = $service->createBody('tambah', $request);

    expect($body)
        ->toBeArray()
        ->toHaveKey('kodebooking', $kodeBooking)
        ->toHaveKey('jenispasien', 'JKN')
        ->toHaveKey('nomorkartu', $request['noBpjs'])
        ->toHaveKey('nik', $request['nik'])
        ->toHaveKey('nohp', $request['noHP'])
        ->toHaveKey('kodepoli', $request['kodePoli'])
        ->toHaveKey('namapoli', $request['namaPoli'])
        ->toHaveKey('pasienBaru', '1')
        ->toHaveKey('norm', $request['noRM'])
        ->toHaveKey('tanggalperiksa', $request['tanggalPeriksa'])
        ->toHaveKey('kodedokter', $request['kodeDokter'])
        ->toHaveKey('namadokter', $request['namaDokter'])
        ->toHaveKey('jampraktek', $request['jamPraktik'])
        ->toHaveKey('jeniskunjungan', 3)
        ->toHaveKey('nomorreferensi', $request['noReferensi'])
        ->toHaveKey('nomorantrean', $request['nomorAntrean'])
        ->toHaveKey('angkaantrean', $request['angkaAntrean'])
        ->toHaveKey('estimasidilayani', $request['estimasiDilayani'])
        ->toHaveKey('sisakuotajkn', $request['sisaKuotaJKN'])
        ->toHaveKey('kuotajkn', $request['kuotaJKN'])
        ->toHaveKey('sisakuotanonjkn', $request['sisaKuotaNonJKN'])
        ->toHaveKey('kuotanonjkn', $request['sisaKuotaNonJKN'])
        ->toHaveKey('keterangan', $request['keterangan']);

    // Create dummy response
    $client->bpjs()->tambah($request);
})->throwsNoExceptions()->skip();

test('update waktu antrean', function () {
    $client = mockAntreanClient([]);

    // Request data
    $kodeBooking = fake()->regexify('[A-Z0-9]{16}');

    $request = [
        'kodeBooking' => $kodeBooking,
        'taskId' => 1,
        'waktu' => 1616559330000,
        'jenisResep' => JenisResepEnum::NON_RACIKAN->value,
    ];

    // Subservice
    $service = $client->bpjs();

    // Create body
    // Assest regular request
    $body = $service->createBody('updateWaktu', $request);

    expect($body)
        ->toBeArray()
        ->toHaveKey('kodebooking', $kodeBooking)
        ->toHaveKey('taskid', $request['taskId'])
        ->toHaveKey('waktu', $request['waktu'])
        ->toHaveKey('jenisresep', $request['jenisResep']);

    // Create dummy response
    $client->bpjs()->updateWaktu($request);
})->throwsNoExceptions()->skip();
