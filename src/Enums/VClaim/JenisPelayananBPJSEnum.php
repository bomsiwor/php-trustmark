<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Enums\EnumToArray;

enum JenisPelayananBPJSEnum: int
{
    use EnumToArray;

    case INAP = 1;
    case JALAN = 2;
}
