<?php

namespace App\LookupTable;

interface LookupTableInterface
{
    public static function generateFromString(string $string): LookupTableDTO;
}