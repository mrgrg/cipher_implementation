<?php

namespace App\Crypto;

use App\Crypto\CryptoInterface;
use App\LookupTable\LookupTableDTO;

class BasicCrypto implements CryptoInterface
{
    protected string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }
    
    public function encrypt(string $text, LookupTableDTO $lookupTable, string $key): string
    {
        return "secret";
    }

    public function decrypt(string $text, LookupTableDTO $lookupTable, string $key): string
    {
        return "plain text";
    }
}