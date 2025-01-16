<?php

namespace Bomsiwor\Trustmark\Core\Clients;

use Bomsiwor\Trustmark\Contracts\ClientContract;
use Bomsiwor\Trustmark\Resources\VClaim\LPK;
use Bomsiwor\Trustmark\Resources\VClaim\Monitoring;
use Bomsiwor\Trustmark\Resources\VClaim\Peserta;
use Bomsiwor\Trustmark\Resources\VClaim\Referensi;
use Bomsiwor\Trustmark\Resources\VClaim\RencanaKontrol;
use Bomsiwor\Trustmark\Resources\VClaim\Rujukan;
use Bomsiwor\Trustmark\Resources\VClaim\SEP;
use Bomsiwor\Trustmark\Transporters\HttpTransporter;

final class VClaimClient implements ClientContract
{
    public function __construct(public readonly HttpTransporter $transporter) {}

    public function peserta(): Peserta
    {
        return new Peserta($this->transporter);
    }

    public function monitoring(): Monitoring
    {
        return new Monitoring($this->transporter);
    }

    public function lpk(): LPK
    {
        return new LPK($this->transporter);
    }

    public function referensi(): Referensi
    {
        return new Referensi($this->transporter);
    }

    public function sep(): SEP
    {
        return new SEP($this->transporter);
    }

    public function rujukan(): Rujukan
    {
        return new Rujukan($this->transporter);
    }

    public function rencanaKontrol(): RencanaKontrol
    {
        return new RencanaKontrol($this->transporter);
    }
}
