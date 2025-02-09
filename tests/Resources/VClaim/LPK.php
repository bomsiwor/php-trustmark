<?php

use function Pest\Faker\fake;

pest()->group('vclaim.lpk');

test('insert', function () {
  // Mock request data
  $noSep = fake()->regexify('[A-Z0-9]{19}');
  $tglMasuk = fake()->date('Y-m-d');
  $tglKeluar = fake()->date('Y-m-d');
  $dpjp = "3abc";
  $user = fake()->name();

  $request = [
    'noSep' => $noSep,
    'tglMasuk' => $tglMasuk,
    'tglKeluar' => $tglKeluar,
    'jaminan' => 1,
    'poli' => "INT",
    'ruangRawat' => 1,
    'kelasRawat' => 1,
    'spesialistik' => "27",
    'caraKeluar' => 1,
    'kondisiPulang' => 1,
    'diagnosis' => [
      ["kode" => "N88.0", "level" => 1],
      ["kode" => "N88.1", "level" => 2],
      ["kode" => "N88.1", "level" => 2],
      ["kode" => "N88.1", "level" => 2],
      ["kode" => "N88.1", "level" => 2],
    ],
    'prosedur' => [
      ["kode" => "N88.0",],
      ["kode" => "N88.1",],
    ],
    'tindakLanjut' => 1,
    'kodePPKRujukan' => "123456789",
    'tglKontrolKembali' => $tglMasuk,
    'poliKontrol' => "INT",
    'dpjp' => $dpjp,
    'user' => $user,
  ];

  // Mock Client
  $client = mockVClaimClient(insertLPK($noSep));

  $service = $client->lpk();

  // Validate body
  $body = $service->createBody('insert', $request);

  // Test the body
  expect($body)->toBeArray();
  expect($body)
    ->toBeArray()
    ->toHaveKey('request.t_lpk.noSep', $request['noSep'])
    ->toHaveKey('request.t_lpk.tglMasuk', $request['tglMasuk'])
    ->toHaveKey('request.t_lpk.tglKeluar', $request['tglKeluar'])
    ->toHaveKey('request.t_lpk.jaminan', $request['jaminan'])
    ->toHaveKey('request.t_lpk.poli.poli', $request['poli'])
    ->toHaveKey('request.t_lpk.perawatan.ruangRawat', $request['ruangRawat'])
    ->toHaveKey('request.t_lpk.perawatan.kelasRawat', $request['kelasRawat'])
    ->toHaveKey('request.t_lpk.perawatan.spesialistik', $request['spesialistik'])
    ->toHaveKey('request.t_lpk.perawatan.caraKeluar', $request['caraKeluar'])
    ->toHaveKey('request.t_lpk.perawatan.kondisiPulang', $request['kondisiPulang'])
    ->toHaveKey('request.t_lpk.rencanaTL.tindakLanjut', $request['tindakLanjut'])
    ->toHaveKey('request.t_lpk.rencanaTL.dirujukKe.kodePPK', $request['kodePPKRujukan'])
    ->toHaveKey('request.t_lpk.rencanaTL.kontrolKembali.tglKontrol', $request['tglKontrolKembali'])
    ->toHaveKey('request.t_lpk.rencanaTL.kontrolKembali.poli', $request['poliKontrol'])
    ->toHaveKey('request.t_lpk.DPJP', $request['dpjp'])
    ->toHaveKey('request.t_lpk.user', $request['user'])
    // Test array payload
    ->and($body['request']['t_lpk']['diagnosa'])
    ->toBeArray()
    ->each(function ($diag) {
      $diag
        ->toHaveKeys(['kode', 'level'])
        ->kode->toBeString()
        ->level->toBeNumeric();
    })
    ->and($body['request']['t_lpk']['procedure'])
    ->toBeArray()
    ->each(function ($proc) {
      $proc
        ->toHaveKeys(['kode'])
        ->kode->toBeString();
    })
  ;

  // Test the response
  $result = $service->insert($request);

  expect($result)->toBeArray();
});

test('update', function () {
  // Mock request data
  $noSep = fake()->regexify('[A-Z0-9]{19}');
  $tglMasuk = fake()->date('Y-m-d');
  $tglKeluar = fake()->date('Y-m-d');
  $dpjp = "3abc";
  $user = fake()->name();

  $request = [
    'noSep' => $noSep,
    'tglMasuk' => $tglMasuk,
    'tglKeluar' => $tglKeluar,
    'jaminan' => 1,
    'poli' => "INT",
    'ruangRawat' => 1,
    'kelasRawat' => 1,
    'spesialistik' => "27",
    'caraKeluar' => 1,
    'kondisiPulang' => 1,
    'diagnosis' => [
      ["kode" => "N88.0", "level" => 1],
      ["kode" => "N88.1", "level" => 2],
    ],
    'prosedur' => [
      ["kode" => "N88.0",],
      ["kode" => "N88.1",],
    ],
    'tindakLanjut' => 1,
    'kodePPKRujukan' => null,
    'tglKontrolKembali' => $tglMasuk,
    'poliKontrol' => "INT",
    'dpjp' => $dpjp,
    'user' => $user,
  ];

  // Mock Client
  $client = mockVClaimClient(insertLPK($noSep));

  $service = $client->lpk();

  // Validate body
  $body = $service->createBody('insert', $request);

  // Test the body
  expect($body)->toBeArray();
  expect($body)
    ->toBeArray()
    ->toHaveKey('request.t_lpk.noSep', $request['noSep'])
    ->toHaveKey('request.t_lpk.tglMasuk', $request['tglMasuk'])
    ->toHaveKey('request.t_lpk.tglKeluar', $request['tglKeluar'])
    ->toHaveKey('request.t_lpk.jaminan', $request['jaminan'])
    ->toHaveKey('request.t_lpk.poli.poli', $request['poli'])
    ->toHaveKey('request.t_lpk.perawatan.ruangRawat', $request['ruangRawat'])
    ->toHaveKey('request.t_lpk.perawatan.kelasRawat', $request['kelasRawat'])
    ->toHaveKey('request.t_lpk.perawatan.spesialistik', $request['spesialistik'])
    ->toHaveKey('request.t_lpk.perawatan.caraKeluar', $request['caraKeluar'])
    ->toHaveKey('request.t_lpk.perawatan.kondisiPulang', $request['kondisiPulang'])
    ->toHaveKey('request.t_lpk.rencanaTL.tindakLanjut', $request['tindakLanjut'])
    ->toHaveKey('request.t_lpk.rencanaTL.dirujukKe.kodePPK', $request['kodePPKRujukan'])
    ->toHaveKey('request.t_lpk.rencanaTL.kontrolKembali.tglKontrol', $request['tglKontrolKembali'])
    ->toHaveKey('request.t_lpk.rencanaTL.kontrolKembali.poli', $request['poliKontrol'])
    ->toHaveKey('request.t_lpk.DPJP', $request['dpjp'])
    ->toHaveKey('request.t_lpk.user', $request['user'])
    // Test array payload
    ->and($body['request']['t_lpk']['diagnosa'])
    ->toBeArray()
    ->each(function ($diag) {
      $diag
        ->kode->toBeString()
        ->level->toBeNumeric();
    })
    ->and($body['request']['t_lpk']['procedure'])
    ->toBeArray()
    ->each(function ($proc) {
      $proc
        ->kode->toBeString();
    })
  ;

  // Test the response
  $result = $service->update($request);

  expect($result)->toBeArray();
});
