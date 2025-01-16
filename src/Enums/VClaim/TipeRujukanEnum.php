<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Enums\EnumToArray;

enum TipeRujukanEnum: int
{
    use EnumToArray;

    case PENUH = 0;
    case PARTIAL = 1;
    case PRB = 2;
}
