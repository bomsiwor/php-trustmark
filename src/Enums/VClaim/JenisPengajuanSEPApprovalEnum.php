<?php

namespace Bomsiwor\Trustmark\Enums\VClaim;

use Bomsiwor\Trustmark\Enums\EnumToArray;

enum JenisPengajuanSEPApprovalEnum: int
{
    use EnumToArray;

    case BACKDATE = 1;
    case FINGER = 2;
}
