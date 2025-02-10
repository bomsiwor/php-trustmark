<?php

namespace Bomsiwor\Trustmark\Core\Clients;

use Bomsiwor\Trustmark\Contracts\ClientContract;
use Bomsiwor\Trustmark\Contracts\DecryptorContract;
use Bomsiwor\Trustmark\Contracts\TransporterContract;
use Bomsiwor\Trustmark\Resources\VClaim\LPK;
use Bomsiwor\Trustmark\Resources\VClaim\Monitoring;
use Bomsiwor\Trustmark\Resources\VClaim\Peserta;
use Bomsiwor\Trustmark\Resources\VClaim\Referensi;
use Bomsiwor\Trustmark\Resources\VClaim\RencanaKontrol;
use Bomsiwor\Trustmark\Resources\VClaim\Rujukan;
use Bomsiwor\Trustmark\Resources\VClaim\SEP;

final class VClaimClient implements ClientContract
{
    public function __construct(public readonly TransporterContract $transporter, public readonly DecryptorContract $decryptor) {}

    public function peserta(): Peserta
    {
        return new Peserta($this->transporter, $this->decryptor);
    }

    public function monitoring(): Monitoring
    {
        return new Monitoring($this->transporter, $this->decryptor);
    }

    public function lpk(): LPK
    {
        return new LPK($this->transporter, $this->decryptor);
    }

    public function referensi(): Referensi
    {
        return new Referensi($this->transporter, $this->decryptor);
    }

    public function sep(): SEP
    {
        return new SEP($this->transporter, $this->decryptor);
    }

    public function rujukan(): Rujukan
    {
        return new Rujukan($this->transporter, $this->decryptor);
    }

    public function rencanaKontrol(): RencanaKontrol
    {
        return new RencanaKontrol($this->transporter, $this->decryptor);
    }
}
