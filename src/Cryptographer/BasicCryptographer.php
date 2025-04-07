<?php

namespace App\Cryptographer;

use App\Attacker\KPACryptanalyserInterface;
use App\Cryptographer\CryptographerInterface;
use App\LookupTable\LookupTableDTO;

class BasicCryptographer implements CryptographerInterface, KPACryptanalyserInterface
{  
    public function encrypt(string $text, LookupTableDTO $lookupTable, string $secretKey): string
    {
        $encryptedNumArr = [];
        $encryptedText = '';

        $numberArrFromText = $this->getNumberArrayFromString($text, $lookupTable);
        $numberArrFromKey = $this->getNumberArrayFromString($secretKey, $lookupTable);

        foreach ($numberArrFromText as $i => $val) {

            $letterCode = $val + $numberArrFromKey[$i];

            if ($letterCode > 26) {
                $letterCode = $letterCode % 27;
            }

            array_push($encryptedNumArr, $letterCode);
        }

        $decoderLUT = $lookupTable->getTableAsStrValues();
        foreach ($encryptedNumArr as $number) {
            $letter = $decoderLUT[$number];
            $encryptedText .= $letter;
        }

        return $encryptedText;
    }

    public function decrypt(string $text, LookupTableDTO $lookupTable, string $secretKey): string
    {

        $decryptedNumArr = [];
        $decryptedText = '';

        $numberArrFromText = $this->getNumberArrayFromString($text, $lookupTable);
        $numberArrFromKey = $this->getNumberArrayFromString($secretKey, $lookupTable);

        foreach ($numberArrFromText as $i => $val) {

            $letterCode = $val - $numberArrFromKey[$i];

            if ($letterCode < 0) {
                $letterCode = $letterCode + 27;
            }

            array_push($decryptedNumArr, $letterCode);
        }

        $decoderLUT = $lookupTable->getTableAsStrValues();
        foreach ($decryptedNumArr as $number) {
            $letter = $decoderLUT[$number];
            $decryptedText .= $letter;
        }

        return $decryptedText;
    }

    public function generateKey(string $crib, string $secretMsg, LookupTableDTO $lookupTable): string
    {
        $key = "";
        $decryptedKeyNumArray = [];

        $numberArrFromMsg = $this->getNumberArrayFromString($crib, $lookupTable);
        $numberArrFromSecrMsg = $this->getNumberArrayFromString($secretMsg, $lookupTable);

        foreach ($numberArrFromMsg as $i => $val) {

            $letterCode = $numberArrFromSecrMsg[$i] - $val;

            if ($letterCode < 0) {
                $letterCode = $letterCode + 27;
            }

            array_push($decryptedKeyNumArray, $letterCode);
        }

        $decoderLUT = $lookupTable->getTableAsStrValues();
        foreach ($decryptedKeyNumArray as $number) {
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