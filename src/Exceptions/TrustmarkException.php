<?php

namespace Bomsiwor\Trustmark\Exceptions;

use Exception;

/**
 * Mendefinisikan Exception dari package VClaim dan memberikan scope pada keterangan error.
 * Scope error dapat berasal dari 'local' berarti error pada saat sebelum atau sesudah mengirim request ke BPJS.
 * Scope 'BPJS' berarti error pada saat request ke BPJS. Bisa jadi dikarenakan oleh API BPJS yang sedang down.
 */
class TrustmarkException extends Exception
{
    public function __construct(
        string $message,
        protected string $scope,
        protected array $data = [],
        int $code = 500,
    ) {
        parent::__construct($scope.' : '.$message, $code);
    }

    public function getErrors(): array
    {
        return $this->data;
    }
}
