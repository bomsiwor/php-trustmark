<?php

function insertLPK(string $noSep): array
{
    return baseVClaimResponse($noSep);
}

function deleteLPK(): array
{
    return baseVClaimResponse([]);
}
