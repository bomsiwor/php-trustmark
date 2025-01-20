<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Traits\EnumToArray;

enum StatusKlaimBPJSEnum: int
{
    use EnumToArray;

    case VERIFIKASI = 1;
    case PENDING = 2;
    case KLAIM = 3;
}
