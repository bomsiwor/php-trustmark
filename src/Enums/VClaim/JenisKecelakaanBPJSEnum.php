<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Traits\EnumToArray;

enum JenisKecelakaanBPJSEnum: int
{
    use EnumToArray;

    case BKLL = 0;
    case KLL_NO_KK = 1;
    case KLL_AND_KK = 2;
    case KK = 3;
}
