<?php

namespace App\LookupTable;
class LookupTableDTO
{
    protected array $tableWithOriginalAsKey;
    protected array $tableWithMorphedAsKey;

    public function __construct(array $lookupTable)
    {
        $this->tableWithOriginalAsKey = $lookupTable;

        $reversedArray = [];

        foreach ($lookupTable as $key => $value) {
            $reversedArray[$value] = $key;
        }

        $this->tableWithMorphedAsKey = $reversedArray;
    }

    public function getTableAsIntValues(): array
    {
        return $this->tableWithOriginalAsKey;
    }

    public function getTableAsStrValues(): array
    {
        return $this->tableWithMorphedAsKey;
    }
}