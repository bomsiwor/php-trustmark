<?php

test('by nik', function () {
    $client = mockVClaimClient(getPesertaByNik());

    // Create dummy response
    $result = $client->peserta()->byNIK('1234567890987654');

    // Asses the response
    expect($result['data'])->toBeArray()
        ->toHaveKey('cob')
        ->toHaveKey('hakKelas')
        ->toHaveKey('informasi')
        ->toHaveKey('jenisPeserta')
        ->toHaveKey('mr')
        ->toHaveKey('nama', 'TRI M')
        ->toHaveKey('nik', '3319022010810007')
        ->toHaveKey('noKartu', '0011336526592')
        ->toHaveKey('pisa', '1')
        ->toHaveKey('provUmum')
        ->toHaveKey('sex', 'L')
        ->toHaveKey('statusPeserta')
        ->toHaveKey('tglCetakKartu', '2016-02-12')
        ->toHaveKey('tglLahir', '1981-10-10')
        ->toHaveKey('tglTAT', '2014-12-31')
        ->toHaveKey('tglTMT', '2008-10-01')
        ->toHaveKey('umur');
});

test('by no bpjs', function () {
    $client = mockVClaimClient(getPesertaByNik());

    // Create dummy response
    $result = $client->peserta()->byNIK('1234567890987654');

    expect($result['data'])->toBeArray()
        ->toHaveKey('cob')
        ->toHaveKey('hakKelas.keterangan', 'KELAS I')
        ->toHaveKey('hakKelas.kode', '1')
        ->toHaveKey('informasi')
        ->toHaveKey('jenisPeserta.keterangan', 'PEGAWAI SWASTA')
        ->toHaveKey('jenisPeserta.kode', '13')
        ->toHaveKey('mr')
        ->toHaveKey('nama', 'TRI M')
        ->toHaveKey('nik', '3319022010810007')
        ->toHaveKey('noKartu', '0011336526592')
        ->toHaveKey('pisa', '1')
        ->toHaveKey('provUmum.kdProvider', '0138U020')
        ->toHaveKey('provUmum.nmProvider', 'KPRJ PALA MEDIKA')
        ->toHaveKey('sex', 'L')
        ->toHaveKey('statusPeserta.keterangan', 'AKTIF')
        ->toHaveKey('statusPeserta.kode', '0')
        ->toHaveKey('tglCetakKartu', '2016-02-12')
        ->toHaveKey('tglLahir', '1981-10-10')
        ->toHaveKey('tglTAT', '2014-12-31')
        ->toHaveKey('tglTMT', '2008-10-01')
        ->toHaveKey('umur.umurSaatPelayanan', '35 tahun ,1 bulan ,11 hari')
        ->toHaveKey('umur.umurSekarang', '35 tahun ,2 bulan ,10 hari');
});
