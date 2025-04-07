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
        $this->secretKey = "abcdefghijklmnopqrstuvwxyz ";   // as long as the longest $encodedMsg
        $chars = "abcdefghijklmnopqrstuvwxyz ";
        $lookupTable = BasicLookupTable::generateFromString($chars);
        $cryptographer = new BasicCryptographer();
        $cipher = new CipherServiceProvider($lookupTable, $cryptographer, $this->secretKey);

        // encrypt given texts
        $curiousCat = $cipher->encode("curiosity killed the cat");      // "cvtlsxo fiutxysspjzxtxwp"
        $earlyBird = $cipher->encode("early bird catches the worm");    // "ebtobehpzmjnmfqwuirlazvslpl"
        $encodedSentencesArr = [$curiousCat, $earlyBird];

        // instantiate the KPA and get keys
        $dictionary = require __DIR__ . "/../assets/dictionary.php";
        $kpa = new KPAttacker($encodedSentencesArr, $lookupTable, $cryptographer, $dictionary);
        $this->generatedKeys = $kpa->getKeysWithEducatedGuess($encodedSentencesArr[1], "early ");
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