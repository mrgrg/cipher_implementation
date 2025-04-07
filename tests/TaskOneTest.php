<?php

namespace Test;

use App\Cryptographer\BasicCryptographer;
use App\LookupTable\BasicLookupTable;
use App\Provider\CipherServiceProvider;
use PHPUnit\Framework\TestCase;

class TaskOneTest extends TestCase
{
    protected string $secretKey;
    protected CipherServiceProvider $cipher;

    protected function setUp(): void
    {
        $chars = "abcdefghijklmnopqrstuvwxyz ";
        $secretKey = "abcdefgijkl";
        $lookupTable = BasicLookupTable::generateFromString($chars);
        $cryptographer = new BasicCryptographer();
        $this->cipher = new CipherServiceProvider($lookupTable, $cryptographer, $secretKey);
    }

    public function testIsMessageCorrectlyEncrypted(): void
    {
        $msg = "helloworld";
        $encryptedMsg = "hfnosauzun";
        $this->assertSame($this->cipher->encrypt($msg), $encryptedMsg);
    }

    public function testIsMessageCorrectlyDecrypted(): void
    {
        $msg = "helloworld";
        $encryptedMsg = "hfnosauzun";
        $this->assertSame($this->cipher->decrypt($encryptedMsg), $msg);
    }
}