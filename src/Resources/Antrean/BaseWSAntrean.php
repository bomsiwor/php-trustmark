<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Resources\Antrean;

use Bomsiwor\Trustmark\Core\PackageValidator;
use Bomsiwor\Trustmark\Enums\VClaim\AssesmentPelayananEnum;
use Bomsiwor\Trustmark\Enums\VClaim\FlagProcedureEnum;
use Bomsiwor\Trustmark\Enums\VClaim\JenisFaskesEnum;
use Bomsiwor\Trustmark\Enums\VClaim\JenisKecelakaanBPJSEnum;
use Bomsiwor\Trustmark\Enums\VClaim\JenisPelayananBPJSEnum;
use Bomsiwor\Trustmark\Enums\VClaim\JenisPengajuanSEPApprovalEnum;
use Bomsiwor\Trustmark\Enums\VClaim\KodePenunjangSEPEnum;
use Bomsiwor\Trustmark\Enums\VClaim\TujuanKunjunganEnum;
use DateTime;
use Respect\Validation\Validator as v;

class BaseWSAntrean
{
    /**
     * Validate the provided data using the given rules.
     *
     * @throws \Exception if validation fails.
     */
    protected function validate(array $data, array $rules): void
    {
        PackageValidator::validate($data, $rules);
    }

    public function createBody(string $key, mixed $data): mixed
    {
        return [];
    }

    /**
     * Get shared validation rules for VCLaim validation
     *
     * This method provides a set of predefined validation rules for VClaim data validation
     * including rules for BPJS number, service types, admission types, and other related fields
     *
     * @return array<string, \Respect\Validation\Validator> Array of validation rules with field names as keys
     */
    protected function getSharedRules(): array
    {
        return [
            'kodePoli' => v::stringType(),
            'kodeSubspesialis' => v::stringType(),
            'kodePPK' => v::stringType()->length(8, 10),
            'tanggal' => v::date('Y-m-d'),
            'tglPelayanan' => v::date('Y-m-d')
                ->oneOf(
                    v::lessThan((new DateTime)->format('Y-m-d')),
                    v::equals((new DateTime)->format('Y-m-d'))
                ),
            'noSep' => v::stringType()->length(19, 19, true),
            'noIdentitas' => v::stringType()->length(11, 16),
            'nik' => v::stringType()->length(16, 16)->setName('NIK'),
            'noBpjs' => v::stringType()->length(13, 15)->setName('Nomor BPJS'),
            'hari' => v::intVal()->between(1, 7),
            'bulan' => v::intVal()->between(1, 12),
            'tahun' => v::intVal()->greaterThan(2000),
            'kodeBooking' => v::stringType()->length(6, null),
            'kodeDokter' => v::nullable(v::stringType()->length(3, null)),
            'keterangan' => v::nullable(v::stringType()),

            'klsRawatHak' => v::intType()->between(1, 3),
            'klsRawatNaik' => v::nullable(v::intType()->between(1, 8)),
            'pembiayaan' => v::nullable(v::intType()->between(1, 3)),
            'penanggungJawab' => v::nullable(v::stringType()),
            'noMR' => v::stringType()->length(5, null),
            'catatan' => v::nullable(v::stringType()),
            'diagAwal' => v::stringType(),
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
            'noTelp' => v::nullable(v::stringType()->length(8)),
            'user' => v::stringType()->length(3, null),
            'noBpjs' => v::stringType()->length(13, 15)->setName('Nomor BPJS'),
            'tglSep' => v::date('Y-m-d'),
            'jnsPelayanan' => v::intType()->in(JenisPelayananBPJSEnum::values()),
            'jnsPengajuan' => v::intType()->in(JenisPengajuanSEPApprovalEnum::values()),
            'noRujukan' => v::stringType()->length(8, null),
            'tujuanKunj' => v::intType()->in(TujuanKunjunganEnum::values()),
            'flagProcedure' => v::intType()->in(FlagProcedureEnum::values()),
            'kdPenunjang' => v::intType()->in(KodePenunjangSEPEnum::values()),
            'assesmentPel' => v::intType()->in(AssesmentPelayananEnum::values()),
            'jenisFaskes' => v::intType()->in(JenisFaskesEnum::values()),
        ];
    }
}
