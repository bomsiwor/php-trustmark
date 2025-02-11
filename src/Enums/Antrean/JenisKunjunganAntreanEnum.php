<?php

namespace Bomsiwor\Trustmark\Enums\Antrean;

use Bomsiwor\Trustmark\Traits\EnumToArray;

enum JenisKunjunganAntreanEnum: int
{
    use EnumToArray;

    case FKTP = 1;
    case RUJUK_INTERNAL = 2;
    case KONTROL = 3;
    case RUJUK_ANTAR_RS = 4;
}
