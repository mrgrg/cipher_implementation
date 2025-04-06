<?php

namespace App\Cryptographer;

use App\Attacker\KPACryptanalyserInterface;
use App\Cryptographer\CryptographerInterface;
use App\LookupTable\LookupTableDTO;

class BasicCryptographer implements CryptographerInterface, KPACryptanalyserInterface
{  
    public function encrypt(string $text, LookupTableDTO $lookupTable, string $secretKey): string
    {
        $encodedNumArr = [];
        $encodedText = '';

        $numberArrFromText = $this->getNumberArrayFromString($text, $lookupTable);
        $numberArrFromKey = $this->getNumberArrayFromString($secretKey, $lookupTable);

        foreach ($numberArrFromText as $i => $val) {

            $letterCode = $val + $numberArrFromKey[$i];

            if ($letterCode > 26) {
                $letterCode = $letterCode % 27;
            }

            array_push($encodedNumArr, $letterCode);
        }

        $decoderLUT = $lookupTable->getTableAsStrValues();
        foreach ($encodedNumArr as $number) {
            $letter = $decoderLUT[$number];
            $encodedText .= $letter;
        }

        return $encodedText;
    }

    public function decrypt(string $text, LookupTableDTO $lookupTable, string $secretKey): string
    {

        $decodedNumArr = [];
        $decodedText = '';

        $numberArrFromText = $this->getNumberArrayFromString($text, $lookupTable);
        $numberArrFromKey = $this->getNumberArrayFromString($secretKey, $lookupTable);

        foreach ($numberArrFromText as $i => $val) {

            $letterCode = $val - $numberArrFromKey[$i];

            if ($letterCode < 0) {
                $letterCode = $letterCode + 27;
            }

            array_push($decodedNumArr, $letterCode);
        }

        $decoderLUT = $lookupTable->getTableAsStrValues();
        foreach ($decodedNumArr as $number) {
            $letter = $decoderLUT[$number];
            $decodedText .= $letter;
        }

        return $decodedText;
    }

    public function generateKey(string $crib, string $secretMsg, LookupTableDTO $lookupTable): string
    {
        $key = "";
        $decodedKeyNumArray = [];

        $numberArrFromMsg = $this->getNumberArrayFromString($crib, $lookupTable);
        $numberArrFromSecrMsg = $this->getNumberArrayFromString($secretMsg, $lookupTable);

        foreach ($numberArrFromMsg as $i => $val) {

            $letterCode = $numberArrFromSecrMsg[$i] - $val;

            if ($letterCode < 0) {
                $letterCode = $letterCode + 27;
            }

            array_push($decodedKeyNumArray, $letterCode);
        }

        $decoderLUT = $lookupTable->getTableAsStrValues();
        foreach ($decodedKeyNumArray as $number) {
            $letter = $decoderLUT[$number];
            $key .= $letter;
        }

        return $key;
    }

    private function getNumberArrayFromString(string $string, LookupTableDTO $LUT): array
    {
        $returnArray = [];
        $letterArr = str_split($string);
        $LUTArr= $LUT->getTableAsIntValues();

        foreach ($letterArr as $letter) {
            array_push($returnArray, $LUTArr[$letter]);
        }

        return $returnArray;
    }

}