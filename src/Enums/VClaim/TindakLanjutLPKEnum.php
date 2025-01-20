<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Traits\EnumToArray;

enum TindakLanjutLPKEnum: int
{
    use EnumToArray;

    case BOLEH_PULANG = 1;
    case PEMERIKSAAN_PENUNJANG = 2;
    case DIRUJUK = 3;
    case KONTROL_KEMBALI = 4;
}
