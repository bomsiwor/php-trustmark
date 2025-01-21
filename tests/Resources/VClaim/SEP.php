<?php

use Bomsiwor\Trustmark\Enums\VClaim\AssesmentPelayananEnum;
use Bomsiwor\Trustmark\Enums\VClaim\FlagProcedureEnum;
use Bomsiwor\Trustmark\Enums\VClaim\JenisFaskesEnum;
use Bomsiwor\Trustmark\Enums\VClaim\JenisKecelakaanBPJSEnum;
use Bomsiwor\Trustmark\Enums\VClaim\JenisPelayananBPJSEnum;
use Bomsiwor\Trustmark\Enums\VClaim\KodePenunjangSEPEnum;
use Bomsiwor\Trustmark\Enums\VClaim\TujuanKunjunganEnum;

use function Pest\Faker\fake;

test('insert SEP v2', function () {
    // Mock request data
    $date = fake()->date('Y-m-d');
    $ppk = fake()->regexify('[A-Za-z0-9]{8}');
    $noBpjs = fake()->regexify('[A-Za-z0-9]{13}');
    $noRujukan = fake()->regexify('[A-Za-z0-9]{18}');
    $noSkdp = fake()->regexify('[A-Za-z0-9]{15}');
    $noMR = fake()->regexify('[A-Za-z0-9]{6}');
    $user = fake()->name();

    $request = [
        'tglSep' => $date,
        'noBpjs' => $noBpjs,
        'ppkPelayanan' => $ppk,
        'jnsPelayanan' => JenisPelayananBPJSEnum::JALAN->value,
        'asalRujukan' => JenisFaskesEnum::FKTP->value,
        'tglRujukan' => $date,
        'noRujukan' => $noRujukan,
        'ppkRujukan' => $ppk,
        'tujuanKunj' => TujuanKunjunganEnum::NORMAL->value,
        'flagProcedure' => FlagProcedureEnum::TIDAK_BERKELANJUTAN->value,
        'kdPenunjang' => KodePenunjangSEPEnum::DEFAULT->value,
        'assesmentPel' => AssesmentPelayananEnum::DEFAULT->value,
        'skdpNoSurat' => $noSkdp,
        'skdpKodeDPJP' => '123456',
        'klsRawatHak' => 1,
        'klsRawatNaik' => null,
        'pembiayaan' => 1,
        'penanggungJawab' => 'Pribadi',
        'noMR' => $noMR,
        'catatan' => null,
        'diagAwal' => 'N18.5',
        'poliTujuan' => 'SAR',
        'poliEksekutif' => false,
        'cob' => false,
        'katarak' => false,
        'jaminanLakaLantas' => JenisKecelakaanBPJSEnum::BKLL->value,
        'jaminanNoLP' => null,
        'jaminanTglKejadian' => null,
        'jaminanKeterangan' => null,
        'jaminanSuplesi' => null,
        'jaminanNoSepSuplesi' => null,
        'jaminanLakaProvinsi' => null,
        'jaminanLakaKabupaten' => null,
        'jaminanLakaKecamatan' => null,
        'dpjpLayan' => '123456',
        'noTelp' => '12345678',
        'user' => $user,
    ];

    // Mock Client
    $client = mockVClaimClient(insertSEP());

    $service = $client->sep();

    // Validate body
    $body = $service->createBody('insertSEP', $request);

    // Test the body
    expect($body)->toBeArray();
    expect($body)
        ->toBeArray()
        ->toHaveKey('request.t_sep.noKartu', $request['noBpjs'])
        ->toHaveKey('request.t_sep.tglSep', $request['tglSep'])
        ->toHaveKey('request.t_sep.ppkPelayanan', $request['ppkPelayanan'])
        ->toHaveKey('request.t_sep.jnsPelayanan', $request['jnsPelayanan'])
        ->toHaveKey('request.t_sep.klsRawat.klsRawatHak', $request['klsRawatHak'])
        ->toHaveKey('request.t_sep.klsRawat.klsRawatNaik', '')
        ->toHaveKey('request.t_sep.klsRawat.pembiayaan', $request['pembiayaan'])
        ->toHaveKey('request.t_sep.klsRawat.penanggungJawab', 'Pribadi')
        ->toHaveKey('request.t_sep.noMR', $request['noMR'])
        ->toHaveKey('request.t_sep.rujukan.asalRujukan', $request['asalRujukan'])
        ->toHaveKey('request.t_sep.rujukan.tglRujukan', $request['tglRujukan'])
        ->toHaveKey('request.t_sep.rujukan.noRujukan', $request['noRujukan'])
        ->toHaveKey('request.t_sep.rujukan.ppkRujukan', $request['ppkRujukan'])
        ->toHaveKey('request.t_sep.catatan', $request['catatan'])
        ->toHaveKey('request.t_sep.diagAwal', $request['diagAwal'])
        ->toHaveKey('request.t_sep.poli.tujuan', $request['poliTujuan'])
        ->toHaveKey('request.t_sep.poli.eksekutif', '0')
        ->toHaveKey('request.t_sep.cob.cob', '0')
        ->toHaveKey('request.t_sep.katarak.katarak', '0')
        ->toHaveKey('request.t_sep.jaminan.lakaLantas', '0')
        ->toHaveKey('request.t_sep.jaminan.noLP', '')
        ->toHaveKey('request.t_sep.jaminan.penjamin.tglKejadian', '')
        ->toHaveKey('request.t_sep.jaminan.penjamin.keterangan', '')
        ->toHaveKey('request.t_sep.jaminan.penjamin.suplesi.suplesi', '')
        ->toHaveKey('request.t_sep.jaminan.penjamin.suplesi.noSepSuplesi', '')
        ->toHaveKey('request.t_sep.jaminan.penjamin.suplesi.lokasiLaka.kdPropinsi', '')
        ->toHaveKey('request.t_sep.jaminan.penjamin.suplesi.lokasiLaka.kdKabupaten', '')
        ->toHaveKey('request.t_sep.jaminan.penjamin.suplesi.lokasiLaka.kdKecamatan', '')
        ->toHaveKey('request.t_sep.tujuanKunj', '0')
        ->toHaveKey('request.t_sep.flagProcedure', '0')
        ->toHaveKey('request.t_sep.kdPenunjang', '0')
        ->toHaveKey('request.t_sep.assesmentPel', '0')
        ->toHaveKey('request.t_sep.skdp.noSurat', $request['skdpNoSurat'])
        ->toHaveKey('request.t_sep.skdp.kodeDPJP', $request['skdpKodeDPJP'])
        ->toHaveKey('request.t_sep.dpjpLayan', $request['dpjpLayan'])
        ->toHaveKey('request.t_sep.noTelp', $request['noTelp'])
        ->toHaveKey('request.t_sep.user', $request['user']);

    // Test the response
    $result = $service->insert($request);

    expect($result)->toBeArray();

});
