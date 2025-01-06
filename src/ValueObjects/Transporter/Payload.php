<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\ValueObjects\Transporter;

use Bomsiwor\Trustmark\Enums\Transporter\ContentType;
use Bomsiwor\Trustmark\Enums\Transporter\Method;
use Bomsiwor\Trustmark\ValueObjects\ResourceUri;
use Http\Discovery\Psr17Factory;
use Psr\Http\Message\RequestInterface;

final class Payload
{
    private function __construct(
        private readonly ResourceUri $resourceUri,
        private readonly Method $method,
        private readonly ?array $parameters = [],
        private readonly ?ContentType $contentType = null,
    ) {}

    /**
     * Handle GET method request. make sure to construct uri with query param.
     * This method does not create or interpolate URI template.
     *
     * @param  string  $uri  URL with query parameters or path variable
     */
    public static function get(string $formatUri, array $pathUri = []): self
    {
        $method = Method::GET;

        $uri = ResourceUri::make($formatUri, $pathUri);

        return new self($uri, $method);
    }

    /**
     * Handle POST method request. make sure to construct URI with query param.
     * THis method can interpolate URI using formatURI or just pass well constructed URI.
     *
     * @param  string  $formatUri  Format URI that will be passed to sprintf or just pass final URI.
     * @param  array  $pathUri  Path URI that will be interpolated to format URI
     * @param  array  $content  Content that will be sent on body
     */
    public static function insert(string $formatUri, array $pathUri = [], array $content = []): self
    {
        $method = Method::POST;

        $contentType = ContentType::URL_ENCODED;

        $uri = ResourceUri::make($formatUri, $pathUri);

        return new self($uri, $method, $content, $contentType);
    }

    public static function update(string $formatUri, array $pathUri = [], array $content = []): self
    {
        $method = Method::PUT;

        $contentType = ContentType::URL_ENCODED;

        $uri = ResourceUri::make($formatUri, $pathUri);

        return new self($uri, $method, $content, $contentType);
    }

    public static function delete(string $formatUri, array $pathUri = [], array $content = []): self
    {
        $method = Method::DELETE;

        $contentType = ContentType::URL_ENCODED;

        $uri = ResourceUri::make($formatUri, $pathUri);

        return new self($uri, $method, $content, $contentType);
    }

    public function toRequest(string $baseUri, array $headers): RequestInterface
    {
        $endpoint = $baseUri.'/'.$this->resourceUri->toString();

        // Generate client instance as an connection preservation
        // And act as request object
        $client = new Psr17Factory;

        $request = $client->createRequest($this->method->value, $endpoint);

        // Add content type
        if ($this->contentType) {
            $request = $request->withHeader('Content-Type', $this->contentType->value);
        }

        // Add headers
        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        // Include body on every request.
        // Even if the method is get, the resource will guarantee to not send body
        // Construct body at json type
        $body = $client->createStream(json_encode($this->parameters, JSON_THROW_ON_ERROR));

        $request = $request->withBody($body);

        return $request;
    }
}
