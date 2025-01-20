<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Traits\EnumToArray;

enum JenisFilterRencanaKontrolEnum: int
{
    use EnumToArray;

    case ENTRI = 1;
    case RENCANA = 2;
}
