<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Core\Signature;

final class VClaimSignature
{
    private function __construct(private string $timestamp, private string $signature) {}

    /**
     * Genearate signature for Header VClaim
     *
     * @param  string  $consumerID  Cons ID dari BPJS
     * @param  string  $consumerKey  consumerKey Consumer Key dari BPJS
     */
    public static function generateSignature(string $consumerID, string $consumerKey): self
    {
        $timestamp = (string) time();

        // Value untuk Hashing
        // Format mengikuti dokumentasi Trustmark
        $data = $consumerID.'&'.$timestamp;

        $signature = hash_hmac('sha256', $data, $consumerKey, true);

        // Encode signature use base64
        $encodedSignature = base64_encode($signature);

        return new self($timestamp, $encodedSignature);
    }

    /**
     * Timestamp untuk header
     *
     * @return string Timestamp
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * Mendapatkan signature yang sudah di encode
     *
     * @return string Encoded signature
     */
    public function getSignature(): string
    {
        return $this->signature;
    }
}
