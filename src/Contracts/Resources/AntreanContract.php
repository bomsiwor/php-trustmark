<?php

namespace Bomsiwor\Trustmark\Contracts\Resources;

interface AntreanContract
{
    /**
     * Mendapatkan resource untuk web service Antrean
     *
     * @param  string  $name  Key untuk resource antrean.
     * @return string resource path untuk antrean. default : 'antrean'
     */
    public function getServiceName(string $name): string;

    public function getValidationRules(array $keys): array;

    public function createBody(string $key, mixed $data): mixed;
}
