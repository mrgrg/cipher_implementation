<?php

namespace App\Provider;

use App\LookupTable\LookupTableDTO;
use App\Crypto\CryptoInterface;
use RuntimeException;

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
        $this->handleSecretKey($secretKey);

        $table = $this->lookupTable;
        $key = $this->secretKey;
        return $this->crypto->encrypt($message, $table, $key);
    }

    public function decode(string $secret, ?string $secretKey = null): string
    {
        $this->handleSecretKey($secretKey);

        $table = $this->lookupTable;
        $key = $this->secretKey;
        return $this->crypto->decrypt($secret, $table, $key);
    }

    // TODO: Checking for $secretKey has valid characters
    // TODO: Make test chases for checking:
    //       injected in constructor | injected in method | error thrown if not set
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