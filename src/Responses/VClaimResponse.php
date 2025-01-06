<?php

declare(strict_types=1);

namespace Bomsiwor\Trustmark\Responses;

use Bomsiwor\Trustmark\Core\Decryptor\VClaimDecryptor;
use Exception;

final class VClaimResponse
{
    public static function from(VClaimDecryptor $decryptor, mixed $result, string $timestamp, bool $obfuscated = true): mixed
    {
        // Decrypt data to get actual content
        if (! array_key_exists('response', $result ?? [])) {
            throw new Exception('Request was not success. There is an error on BPJS server or on the package.');
        }

        // Generate response message
        $result['message'] = $result['metaData']['message'] ?? $result['metadata']['message'];

        // Generate response by unpack the result
        if (! $result['response']) {
            $result['data'] = null;

            unset($result['response']);

            return $result;
        }

        // Decrypt data if obfuscated
        if ($obfuscated) {
            $decryptedData = $decryptor
                ->decryptData($timestamp, $result['response'])
                ->result();
        } else {
            $decryptedData = $result['response'];
        }

        // Unpack the response data
        // Unpack if the data is object.
        // This does not unpack data if the data has more than one property
        // decryptedData variabel only exist when the data is marked as obfuscated
        $result['data'] = $decryptedData;

        self::unpackResponse($decryptedData, $result);

        unset($result['response']);

        return $result;
    }

    /**
     * Unpack the object if there is only one property
     *
     * @param  array|object  $data  Object which will get unpacked
     * @param  array  &$resultSet  Pointer to original result data that persist the data
     */
    public static function unpackResponse(mixed $data, array &$resultSet)
    {
        if (is_object($data)) {
            $properties = get_object_vars($data);

            // Dont extract first property if there is any other properties
            if (count($properties) > 1) {
                $resultSet['data'] = $data;

                return;
            }

            // First property on data object
            $firstProperty = array_key_first($properties);

            $resultSet['data'] = $data->$firstProperty;

            return;
        }

        $resultSet['data'] = $data;
    }
}
