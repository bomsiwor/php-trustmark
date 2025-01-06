<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Core\Decryptor;

use Bomsiwor\Trustmark\Contracts\DecryptorContract;
use Exception;
use LZCompressor\LZString;

final class VClaimDecryptor implements DecryptorContract
{
    private string $consId;

    private string $secretKey;

    /**
     * @var mixed Menyimpan data asli setelah proses dekripsi. Data ini masih berbentuk stringified JSON.
     */
    private mixed $actualContent;

    public function __construct(string $consId, string $secretKey)
    {
        $this->consId = $consId;
        $this->secretKey = $secretKey;
    }

    /**
     * Decrypt and decompress the obfuscated data.
     *
     * @param  string  $timestamp  The timestamp used for the decryption key.
     * @param  string  $obfuscatedData  The data to decrypt and decompress.
     * @return $this Decrypted and decompressed data or null if unsuccessful.
     */
    public function decryptData(string $timestamp, string $obfuscatedData): self
    {
        $key = $this->generateDecryptionKey($timestamp);

        $decrypted = $this->decrypt($key, $obfuscatedData);

        $this->actualContent = $this->decompress($decrypted);

        return $this;
    }

    /**
     * Generate the decryption key using the provided timestamp.
     */
    private function generateDecryptionKey(string $timestamp): string
    {
        return $this->consId.$this->secretKey.$timestamp;
    }

    /**
     * Perform decryption on the obfuscated data using AES-256-CBC.
     */
    private function decrypt(string $key, string $data): ?string
    {
        $encryptMethod = 'AES-256-CBC';

        $hashedKey = hex2bin(hash('sha256', $key));

        $iv = substr($hashedKey, 0, 16);

        return openssl_decrypt(
            base64_decode($data),
            $encryptMethod,
            $hashedKey,
            OPENSSL_RAW_DATA,
            $iv
        ) ?: null;
    }

    /**
     * Decompress the given string using LZString.
     */
    private function decompress(?string $compressedData): ?string
    {
        return $compressedData ? LZString::decompressFromEncodedURIComponent($compressedData) : null;
    }

    /**
     * Return actual data after decoded from JSON.
     *
     * @return mixed Actual data (object shaped, not an array)
     */
    public function result(): mixed
    {
        if (! $this->actualContent) {
            throw new Exception('Actual content is empty! or you use this method incorrectly. Use decrypt first, then use result method');
        }

        return json_decode($this->actualContent);
    }
}
