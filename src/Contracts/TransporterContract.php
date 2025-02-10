<?php

namespace Bomsiwor\Trustmark\Contracts;

use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;

interface TransporterContract
{
    public function sendRequest(Payload $payload);

    public function getConfig(?string $key = null);

    public function getTimestamp(): string;
}
