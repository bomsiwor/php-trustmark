<?php

namespace Bomsiwor\Trustmark\Enums\Antrean;

use Bomsiwor\Trustmark\Traits\EnumToArray;

enum JenisResepEnum: string
{
    use EnumToArray;

    case NONE = 'Tidak ada';
    case RACIKAN = 'racikan';
    case NON_RACIKAN = 'non racikan';
}
