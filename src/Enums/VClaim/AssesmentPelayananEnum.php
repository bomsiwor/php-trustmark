<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Traits\EnumToArray;

enum AssesmentPelayananEnum: int
{
    use EnumToArray;

    case DEFAULT = 0;
    case POLI_TIDAK_TERSEDIA = 1;
    case JAM_POLI_HABIS = 2;
    case DOKTER_TIDAK_TERSEDIA = 3;
    case INSTRUKSI_RS = 4;
    case KONTROL = 5;
}
