<?php

use App\Crypto\BasicCrypto;
use App\LookupTable\BasicLookupTable;
use App\Provider\CipherServiceProvider;

require_once 'vendor/autoload.php';

$secretKey = 'xy';

$chars = "abcdefghijklmnopqrstuvwxyz ";
$lookupTable = BasicLookupTable::generateFromString($chars);
$crypto = new BasicCrypto("testKey");

$chiper = new CipherServiceProvider($lookupTable, $crypto, $secretKey);

var_dump($chiper->decode("qwertz"));
var_dump($chiper->encode("qwertz"));