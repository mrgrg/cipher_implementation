<?php

namespace Test;

use App\Attacker\KPAttacker;
use App\Cryptographer\BasicCryptographer;
use App\LookupTable\BasicLookupTable;
use App\Provider\CipherServiceProvider;
use PHPUnit\Framework\TestCase;

class TaskTwoTest extends TestCase
{
    protected string $secretKey;
    protected array $generatedKeys;

    protected function setUp(): void
    {

        // setup cipher so plain texts can be encrypted
        $this->secretKey = "abcdefghijklmnopqrstuvwxyz ";   // as long as the longest encrypted msg
        $chars = "abcdefghijklmnopqrstuvwxyz ";
        $lookupTable = BasicLookupTable::generateFromString($chars);
        $cryptographer = new BasicCryptographer();
        $cipher = new CipherServiceProvider($lookupTable, $cryptographer, $this->secretKey);

        // encrypt given texts
        $curiousCat = $cipher->encrypt("curiosity killed the cat");      // "cvtlsxo fiutxysspjzxtxwp"
        $earlyBird = $cipher->encrypt("early bird catches the worm");    // "ebtobehpzmjnmfqwuirlazvslpl"
        $encryptedSentencesArr = [$curiousCat, $earlyBird];

        // instantiate the KPA and get keys
        $dictionary = require __DIR__ . "/../assets/dictionary.php";
        $kpa = new KPAttacker($encryptedSentencesArr, $lookupTable, $cryptographer, $dictionary);
        $this->generatedKeys = $kpa->getKeysWithEducatedGuess($encryptedSentencesArr[1], "early ");
    }

    public function testIsOriginalKeyInResultSet(): void
    {
        $this->assertContains($this->secretKey, $this->generatedKeys, "Original secret key exists in the result set!");
    }

    public function testAreResultKeysUnique(): void
    {
        $untouchedLength = count($this->generatedKeys);
        $duplicationRemovedLength = count(array_unique($this->generatedKeys));

        $this->assertEquals($untouchedLength, $duplicationRemovedLength, "Result set holds unique values!");
    }
}