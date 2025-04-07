<?php

namespace App\Attacker;

use App\Attacker\KPACryptanalyserInterface;
use App\LookupTable\LookupTableDTO;

class KPAttacker
{
    private array $encryptedMsgArray;
    private LookupTableDTO $lookupTable;
    private KPACryptanalyserInterface $cryptographer;
    private array $dictionary;
    private array $keys = [];

    public function __construct(
        array $encryptedMsgArray,
        LookupTableDTO $lookupTable,
        KPACryptanalyserInterface $cryptographer,
        array $dictionary,
    ) {
        $this->encryptedMsgArray = $encryptedMsgArray;
        $this->lookupTable = $lookupTable;
        $this->cryptographer = $cryptographer;
        $this->dictionary = $dictionary;
    }

    public function getKeysWithEducatedGuess($encryptedMsg, $crib): array
    {
        $this->getKeysWithIteration($encryptedMsg, $crib);
        return $this->keys;
    }

    private function getKeysWithIteration($encryptedMsg, $crib = null, $keyFragment = null): void
    {
        // ignore at first iteration
        // get crib from extended key 
        if (is_null($crib)) {
            $encryptedMsgSegment = substr($encryptedMsg, 0, strlen($keyFragment));
            $crib = $this->cryptographer->decrypt($encryptedMsgSegment, $this->lookupTable, $keyFragment);
        }

        // stop iteration if crib is overflowed
        if ($this->isCribOverflowed($crib, $encryptedMsg)) {
            return;
        }

        // check if crib is a valid solution
        if ($this->isCribValid($crib, $encryptedMsg) && ! in_array($keyFragment, $this->keys)) {
            $this->recordKeyAsSolved($keyFragment);
            return;
        }

        // prepare for next iteration
        $nextEncryptedMsg = $this->getNextEncryptedMsg($encryptedMsg);

        // only at first iteration: generate the key from guess
        if (is_null($keyFragment)) {
            $keyFragment = $this->cryptographer->generateKey($crib, $encryptedMsg, $this->lookupTable);
            $this->getKeysWithIteration($nextEncryptedMsg, keyFragment: $keyFragment);
        }
        // after first iteration
        else {
            $extendedCribs = $this->getExtendedCribs($crib, $encryptedMsg);
            foreach ($extendedCribs as $newCrib) {
                $extendedKey = $this->cryptographer->generateKey($newCrib, $encryptedMsg, $this->lookupTable);
                $this->getKeysWithIteration($nextEncryptedMsg, keyFragment: $extendedKey);
            }
        }
    }

    private function isCribValid($crib, $encryptedMsg): bool
    {

        if (strlen($crib) != strlen($encryptedMsg)) {
            return false;
        }

        return true;

    }

    private function isCribOverflowed($crib, $encryptedMsg): bool
    {
        if (strlen($crib) > strlen($encryptedMsg)) {
            return true;
        }

        return false;
    }

    private function getExtendedCribs($crib, $encryptedMsg): array
    {
        // pre-instantiated for cases where return array is empty
        $returnArr = [];

        $wordsArr = explode(" ", $crib);
        $lastChunk = $wordsArr[sizeof($wordsArr) - 1];

        // crib is generated from guessed secret key,
        // sometimes this key extend the crib with 2
        // extra word: return if penultimate is inalid
        if (isset($wordsArr[sizeof($wordsArr) - 2])) {
            if ( ! in_array($wordsArr[sizeof($wordsArr) - 2], $this->dictionary)) {
                return $returnArr;
            }
        }

        // get all words from dictionary which can be used for later guess
        $possibleWords = preg_grep("/\b" . $lastChunk . "\w*\b/", $this->dictionary);

        // return without any result to indeicate dead end
        if (! count($possibleWords)) {
            return $returnArr;
        }

        // reconstruct partial cribs with guess
        foreach ($possibleWords as $word) {
            $wordsArr[sizeof($wordsArr) - 1] = $word;
            $extendedCrib = implode(" ", $wordsArr);

            // add space at the end if it's not already present
            // or does not have the exact length as the encrypted one
            if (substr($extendedCrib, -1) != " " && strlen($encryptedMsg) != strlen($extendedCrib)) {
                $extendedCrib = $extendedCrib . " ";
            }

            // only push to array if not overflowed
            if (strlen($encryptedMsg) >= strlen($extendedCrib)) {
                array_push($returnArr, $extendedCrib);
            }
        }

        return $returnArr;
    }

    private function getNextEncryptedMsg($encryptedMsg): string
    {
        $numOfObservableMsg = count($this->encryptedMsgArray);
        $indexOfCurrentlyObserved = array_search($encryptedMsg, $this->encryptedMsgArray);
        $nextEncryptedMsg = ($indexOfCurrentlyObserved + 1) % $numOfObservableMsg;

        return $this->encryptedMsgArray[$nextEncryptedMsg];
    }

    private function recordKeyAsSolved($key): void
    {

        foreach($this->encryptedMsgArray as $encryptedMsg) {

            if (strlen($encryptedMsg) > strlen($key)) {
                return;
            }

            // double check if decrypted msg with key
            // only holds words from the dictionary
            $decryptedMsg = $this->cryptographer->decrypt($encryptedMsg, $this->lookupTable, $key);
            $words = explode(" ", $decryptedMsg);
            foreach ($words as $word) {
                if ( ! in_array($word, $this->dictionary)) {
                   return;
                }
                
            }
        }

        if ( ! in_array($key, $this->keys)) {
            array_push($this->keys, $key);
        }
    }

}
