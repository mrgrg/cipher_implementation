<?php

namespace App\LookupTable;

use App\LookupTable\LookupTableInterface;
use App\LookupTable\LookupTableDTO;

class BasicLookupTable implements LookupTableInterface
{
    public static function generateFromString(string $string): LookupTableDTO {
        $array = str_split($string);
        $lookupTable = [];

        // Since $array is an indexed array, and the
        // required outcome needs the values as indexes and the 
        // indexes as values just flip the pairs
        foreach ($array as $key => $value) {
            $lookupTable[$value] = $key;
        }

        return new LookupTableDTO($lookupTable);
    }
}