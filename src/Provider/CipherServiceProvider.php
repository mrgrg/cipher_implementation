<?php

namespace App\Provider;

use App\LookupTable\LookupTableDTO;
use App\Crypto\CryptoInterface;

class CipherServiceProvider
{
    private LookupTableDTO $lookupTable;
    private CryptoInterface $crypto;
    private ?string $secretKey;

    public function __construct(LookupTableDTO $lookupTable, CryptoInterface $crypto, ?string $secretKey = null) {
        $this->lookupTable = $lookupTable;
        $this->crypto = $crypto;
        $this->secretKey = $secretKey;
    }

    public function getLookupTable(): LookupTableDTO
    {
        return $this->lookupTable;
    }

    public function getCrypto(): CryptoInterface
    {
        return $this->crypto;
    }

    public function encode(string $message, ?string $secretKey = null): string
    {
        if (! is_null($secretKey)) {
            $this->setSecretKey($secretKey);
        }

        $table = $this->lookupTable;
        $key = $this->secretKey;
        return $this->crypto->encrypt($message, $table, $key);
    }

    public function decode(string $secret, ?string $secretKey = null): string
    {
        if (! is_null($secretKey)) {
            $this->setSecretKey($secretKey);
        }

        $table = $this->lookupTable;
        $key = $this->secretKey;
        return $this->crypto->decrypt($secret, $table, $key);
    }

    public function setSecretKey(string $secter): void
    {
        $this->secretKey = $secter;
    }
    
}