<?php

namespace App\Attacker;

use App\Cryptographer\CryptographerInterface;
use App\LookupTable\LookupTableDTO;

interface KPACryptanalyserInterface extends CryptographerInterface
{
    public function generateKey(string $crib, string $secretMsg, LookupTableDTO $lookupTable): string;
}