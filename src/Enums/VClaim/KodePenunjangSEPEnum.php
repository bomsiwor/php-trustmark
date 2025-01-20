<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Traits\EnumToArray;

enum KodePenunjangSEPEnum: int
{
    use EnumToArray;

    case DEFAULT = 0;
    case RADIOTERAPI = 1;
    case KEMOTERAPI = 2;
    case REHABILITASI_MEDIK = 3;
    case REHABILITASI_PSIKOSOSIAL = 4;
    case TRANSFUSI_DARAH = 5;
    case GIGI = 6;
    case LABORATORIUM = 7;
    case USG = 8;
    case FARMASI = 9;
    case LAIN = 10;
    case MRI = 11;
    case HEMODIALISIS = 12;
}
