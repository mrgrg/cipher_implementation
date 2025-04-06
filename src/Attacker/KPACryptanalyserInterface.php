<?php

namespace App\Attacker;

use App\LookupTable\LookupTableDTO;

interface KPACryptanalyserInterface
{
    public function generateKey(string $crib, string $secretMsg, LookupTableDTO $lookupTable): string;
}