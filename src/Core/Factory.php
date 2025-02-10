<?php

namespace Bomsiwor\Trustmark\Core;

use Bomsiwor\Trustmark\Contracts\ClientContract;
use Bomsiwor\Trustmark\Core\Clients\AntreanClient;
use Bomsiwor\Trustmark\Core\Clients\VClaimClient;
use Bomsiwor\Trustmark\Core\Decryptor\VClaimDecryptor;
use Bomsiwor\Trustmark\Transporters\HttpTransporter;
use Http\Discovery\Psr18ClientDiscovery;

final class Factory
{
    private ?string $timestamp = null;

    private array $config = [];

    /**
     * @var string BaseURL untuk setiap WS
     */
    private ?string $baseUrl = null;

    private array $headers = [];

    public function __construct(private string $clientClass) {}

    public function withBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Adds a custom HTTP header to the requests.
     */
    public function withHttpHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function withTimestamp(string $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function withConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function make(): ClientContract
    {
        // Create client using PSR18
        // This client that will act to send request to created request
        $client = Psr18ClientDiscovery::find();

        // Pass client to transporter
        $transporter = new HttpTransporter($client, $this->baseUrl, $this->headers, $this->timestamp, $this->config);

        // Add decryptor condition
        switch ($this->clientClass) {
            case VClaimClient::class:
                $decryptor = new VClaimDecryptor($transporter->getConfig('consId'), $transporter->getConfig('secretKey'));
                break;
            case AntreanClient::class:
                $decryptor = new VClaimDecryptor($transporter->getConfig('consId'), $transporter->getConfig('secretKey'));
                break;
        }

        // COnstruct client
        return new $this->clientClass($transporter, $decryptor);
    }
}
