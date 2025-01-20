<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Traits\EnumToArray;

enum JenisDiagnosisEnum: int
{
    use EnumToArray;

    case PRIMER = 1;
    case SEKUNDER = 2;
}
