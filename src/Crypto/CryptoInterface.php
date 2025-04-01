<?php

namespace App\Crypto;

use App\LookupTable\LookupTableDTO;

interface CryptoInterface
{
    public function encrypt(string $text, LookupTableDTO $array, string $key): string;
    public function decrypt(string $text, LookupTableDTO $array, string $key): string;
}