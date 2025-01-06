<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Transporters;

use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;
use Psr\Http\Client\ClientInterface;

final class HttpTransporter
{
    public function __construct(
        private ?ClientInterface $client,
        private string $baseUrl,
        private array $headers,
        private string $timestamp,
        private array $config = [],
    ) {
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function sendRequest(Payload $payload): mixed
    {
        // Enchance and fill up the request instance with base URl and headers
        $request = $payload->toRequest($this->baseUrl, $this->headers);

        // Send actual request
        $response = $this->client->sendRequest($request);

        // Get content after retrieving response from user
        $content = $response->getBody()->getContents();

        // Decode json response
        $result = json_decode($content, true);

        return $result;
    }

    public function getConfig(?string $key = null): mixed
    {
        return $this->config[$key] ?? null;
    }
}
