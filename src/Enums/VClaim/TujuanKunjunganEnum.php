<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Enums\EnumToArray;

enum TujuanKunjunganEnum: int
{
    use EnumToArray;

    case NORMAL = 0;
    case PROSEDUR = 1;
    case KONSUL_DOKTER = 2;
}