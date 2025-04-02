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

    public function testIsMessageCorrectlyEncoded(): void
    {
        $msg = "helloworld";
        $encryptedMsg = "hfnosauzun";
        $this->assertSame($this->cipher->encode($msg), $encryptedMsg);
    }

    public function testIsMessageCorrectlyDecoded(): void
    {
        $msg = "helloworld";
        $encryptedMsg = "hfnosauzun";
        $this->assertSame($this->cipher->decode($encryptedMsg), $msg);
    }
}