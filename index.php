<?php

require_once "vendor/autoload.php";

use App\Cryptographer\BasicCryptographer;
use App\LookupTable\BasicLookupTable;
use App\Provider\CipherServiceProvider;

$secretKey = "abcdefgijkl";

$chars = "abcdefghijklmnopqrstuvwxyz ";
$lookupTable = BasicLookupTable::generateFromString($chars);
$cryptographer = new BasicCryptographer();

$chiper = new CipherServiceProvider($lookupTable, $cryptographer, $secretKey);

echo $chiper->decode("hfnosauzun");
echo $chiper->encode("helloworld");