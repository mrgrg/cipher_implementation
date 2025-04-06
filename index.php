<?php

require_once "vendor/autoload.php";

use App\Attacker\KPAttacker;
use App\Cryptographer\BasicCryptographer;
use App\LookupTable\BasicLookupTable;
use App\Provider\CipherServiceProvider;

// set up
$secretKey = "abcdefghijklmnopqrstuvwxyz ";
$chars = "abcdefghijklmnopqrstuvwxyz ";

// instantiate the chiper object with its dependencies
$lookupTable = BasicLookupTable::generateFromString($chars);
$cryptographer = new BasicCryptographer();
$chiper = new CipherServiceProvider($lookupTable, $cryptographer, $secretKey);

// encode the messages giwen in the task
$curiousCat = $chiper->encode("curiosity killed the cat");      // "cvtlsxo fiutxysspjzxtxwp"
$earlyBird = $chiper->encode("early bird catches the worm");    // "ebtobehpzmjnmfqwuirlazvslpl"

// instantiate the bruteForce object with its dependencies
$encodedSentencesArr = [$curiousCat, $earlyBird];
$dictionary = require './assets/dictionary.php';
$bruteForcer = new KPAttacker($encodedSentencesArr, $lookupTable, $cryptographer, $dictionary);

// log the key's
$possibleKeys = $bruteForcer->getKeysWithEducatedGuess($encodedSentencesArr[1], "early ");
/* var_dump($possibleKeys); */
foreach($possibleKeys as $key) {
    var_dump($cryptographer->decrypt($earlyBird, $lookupTable, $key));
}