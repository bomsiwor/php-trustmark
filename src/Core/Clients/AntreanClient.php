<?php

namespace Bomsiwor\Trustmark\Core\Clients;

use Bomsiwor\Trustmark\Contracts\ClientContract;
use Bomsiwor\Trustmark\Contracts\DecryptorContract;
use Bomsiwor\Trustmark\Contracts\TransporterContract;
use Bomsiwor\Trustmark\Resources\Antrean\AntreanBPJS;

final class AntreanClient implements ClientContract
{
    public function __construct(public readonly TransporterContract $transporter, public readonly DecryptorContract $decryptor) {}

    public function bpjs(): AntreanBPJS
    {
        return new AntreanBPJS($this->transporter, $this->decryptor);
    }
}
