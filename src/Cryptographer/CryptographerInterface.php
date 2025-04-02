<?php

namespace App\Cryptographer;

use App\LookupTable\LookupTableDTO;

interface CryptographerInterface
{
    public function encrypt(string $text, LookupTableDTO $array, string $key): string;
    public function decrypt(string $text, LookupTableDTO $array, string $key): string;
}