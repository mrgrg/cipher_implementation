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
$cipher = new CipherServiceProvider($lookupTable, $cryptographer, $secretKey);

// encrypt the messages giwen in the task
$curiousCat = $cipher->encrypt("curiosity killed the cat");      // "cvtlsxo fiutxysspjzxtxwp"
$earlyBird = $cipher->encrypt("early bird catches the worm");    // "ebtobehpzmjnmfqwuirlazvslpl"

// instantiate the bruteForce object with its dependencies
$encryptedSentencesArr = [$curiousCat, $earlyBird];
$dictionary = require './assets/dictionary.php';
$bruteForcer = new KPAttacker($encryptedSentencesArr, $lookupTable, $cryptographer, $dictionary);

// log the key's
$possibleKeys = $bruteForcer->getKeysWithEducatedGuess($encryptedSentencesArr[1], "early ");
/* var_dump($possibleKeys); */
foreach($possibleKeys as $key) {
    var_dump($cipher->decrypt($earlyBird, $key));
}