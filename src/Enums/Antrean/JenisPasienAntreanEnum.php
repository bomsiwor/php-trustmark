<?php

namespace Bomsiwor\Trustmark\Enums\Antrean;

use Bomsiwor\Trustmark\Traits\EnumToArray;

enum JenisPasienAntreanEnum: string
{
    use EnumToArray;

    case JKN = 'JKN';
    case NON_JKN = 'NON JKN';
}
