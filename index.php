<?php

require_once "vendor/autoload.php";

use App\Crypto\BasicCrypto;
use App\LookupTable\BasicLookupTable;
use App\Provider\CipherServiceProvider;

$secretKey = "abcdefgijkl";

$chars = "abcdefghijklmnopqrstuvwxyz ";
$lookupTable = BasicLookupTable::generateFromString($chars);
$crypto = new BasicCrypto("testKey");

$chiper = new CipherServiceProvider($lookupTable, $crypto, $secretKey);

echo $chiper->decode("hfnosauzun");
echo $chiper->encode("helloworld");