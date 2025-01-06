<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Enums\EnumToArray;

enum FlagProcedureEnum: int
{
    use EnumToArray;

    case TIDAK_BERKELANJUTAN = 0;
    case BERKELANJUTAN = 1;
}