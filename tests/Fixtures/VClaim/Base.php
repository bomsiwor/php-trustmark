<?php

function baseVClaimResponse(mixed $data): array
{
    return [
        'metaData' => [
            'message' => 'Sukses',
        ],
        'response' => json_encode($data),
    ];
}
