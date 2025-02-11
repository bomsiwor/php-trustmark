<?php

function baseAntreanResponse(mixed $data): array
{
    return [
        'metaData' => [
            'message' => 'Sukses',
        ],
        'response' => json_encode($data),
    ];
}
