<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Enums\EnumToArray;

enum JenisFaskesEnum: int
{
    use EnumToArray;

    case FKTP = 1;
    case RS = 2;
}
