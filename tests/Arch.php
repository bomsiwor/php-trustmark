<?php

arch('Contracts')
    ->expect('Bomsiwor\Trustmark\Contracts')
    ->toBeInterfaces();

arch('Enums')
    ->expect('Bomsiwor\Trustmark\Enums')
    ->toBeEnums();

arch('Traits')
    ->expect('Bomsiwor\Trustmark\Traits')
    ->toBeTraits();

arch('Exceptions')
    ->expect('Bomsiwor\Trustmark\Exceptions')
    ->toBeClasses()
    ->toExtend(Exception::class);
