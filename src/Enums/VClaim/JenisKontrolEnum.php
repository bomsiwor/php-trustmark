<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Traits\EnumToArray;

enum JenisKontrolEnum: int
{
    use EnumToArray;

    case SPRI = 1;
    case KONTROL = 2;
}
