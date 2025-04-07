<?php

namespace App\Provider;

use App\LookupTable\LookupTableDTO;
use App\Cryptographer\CryptographerInterface;
use RuntimeException;

class CipherServiceProvider
{
    private LookupTableDTO $lookupTable;
    private CryptographerInterface $cryptographer;
    private ?string $secretKey;

    public function __construct(LookupTableDTO $lookupTable, CryptographerInterface $cryptographer, ?string $secretKey = null)
    {
        $this->lookupTable = $lookupTable;
        $this->cryptographer = $cryptographer;
        $this->secretKey = $secretKey;
    }

    public function getLookupTable(): LookupTableDTO
    {
        return $this->lookupTable;
    }

    public function getCryptographer(): CryptographerInterface
    {
        return $this->cryptographer;
    }

    public function encrypt(string $message, ?string $secretKey = null): string
    {
        $this->handleSecretKey($secretKey);

        $table = $this->lookupTable;
        $key = $this->secretKey;
        return $this->cryptographer->encrypt($message, $table, $key);
    }

    public function decrypt(string $secret, ?string $secretKey = null): string
    {
        $this->handleSecretKey($secretKey);

        $table = $this->lookupTable;
        $key = $this->secretKey;
        return $this->cryptographer->decrypt($secret, $table, $key);
    }

    public function handleSecretKey($secretKey): void
    {
        if (! is_null($secretKey)) {
            $this->secretKey = $secretKey;
        }

        if (is_null($this->secretKey)) {
            throw new RuntimeException("A secret key must be provided!");
        }
    }

    public function setSecretKey(string $secretKey): void
    {
        $this->secretKey = $secretKey;
    }
    
}