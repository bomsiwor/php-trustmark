<?php

arch('Contracts')
    ->expect('Bomsiwor\Trustmark\Contracts')
    ->toBeInterfaces();

arch('Enums')
    ->expect('Bomsiwor\Trustmark\Enums')
    ->toBeEnums();

arch('VClaim Enum')
    ->expect('Bomsiwor\Trustmark\Enums\VClaim')
    ->toUseTrait('Bomsiwor\Trustmark\Traits\EnumToArray');

arch('Traits')
    ->expect('Bomsiwor\Trustmark\Traits')
    ->toBeTraits();

arch('Exceptions')
    ->expect('Bomsiwor\Trustmark\Exceptions')
    ->toBeClasses()
    ->toExtend(Exception::class);
