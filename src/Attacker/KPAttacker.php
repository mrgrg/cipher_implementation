<?php

namespace App\Attacker;

use App\Cryptographer\BasicCryptographer;
use App\LookupTable\LookupTableDTO;

class KPAttacker
{
    private array $encryptedMsgArray;
    private LookupTableDTO $lookupTable;
    private BasicCryptographer $cryptographer;
    private array $dictionary;
    public array $keys = [];

    public function __construct(
        array $encryptedMsgArray,
        LookupTableDTO $lookupTable,
        BasicCryptographer $cryptographer,
        array $dictionary,
    ) {
        $this->encryptedMsgArray = $encryptedMsgArray;
        $this->lookupTable = $lookupTable;
        $this->cryptographer = $cryptographer;
        $this->dictionary = $dictionary;
    }

    public function getKeysWithEducatedGuess($encodedMsg, $crib): array
    {
        $this->getKeysWithIteration($encodedMsg, $crib);
        return $this->keys;
    }

    private function getKeysWithIteration($encodedMsg, $crib = null, $keyFragment = null): void
    {
        // excep from the first iteration
        // crib must be decoded
        if (is_null($crib)) {
            $encodedMsgSegment = substr($encodedMsg, 0, strlen($keyFragment));
            $crib = $this->cryptographer->decrypt($encodedMsgSegment, $this->lookupTable, $keyFragment);
        }

        // stop iteration if crib is overflowed
        if ($this->isCribOverflowed($crib, $encodedMsg)) {
            return;
        }

        // check if crib can be a valid solution
        if ($this->isCribValid($crib, $encodedMsg) && ! in_array($keyFragment, $this->keys)) {
            $this->recordKeyAsSolved($keyFragment);
            //array_push($this->keys, $keyFragment);
            return;
        }

        // for next iteration
        $nextEncodedMsg = $this->getNextEncodedMsg($encodedMsg);

        // at first iteration: generate the key from guess
        // other iterations: it is already given
        if (is_null($keyFragment)) {
            $keyFragment = $this->cryptographer->generateKey($crib, $encodedMsg, $this->lookupTable);
            $this->getKeysWithIteration($nextEncodedMsg, keyFragment: $keyFragment);
        }

        // if this is the very first entry,
        // than these part must be skipped
        else {
            $extendedCribs = $this->getExtendedCribs($crib, $encodedMsg);
            foreach ($extendedCribs as $newCrib) {
                /* usleep(30000);
                var_dump($newCrib); */
                $extendedKey = $this->cryptographer->generateKey($newCrib, $encodedMsg, $this->lookupTable);
                $this->getKeysWithIteration($nextEncodedMsg, keyFragment: $extendedKey);
            }
        }
    }

    private function isCribValid($crib, $encodedMsg)
    {

        if (strlen($crib) != strlen($encodedMsg)) {
            return false;
        }

        return true;

    }

    private function isCribOverflowed($crib, $encodedMsg)
    {
        if (strlen($crib) > strlen($encodedMsg)) {
            return true;
        }

        return false;
    }

    private function getExtendedCribs($crib, $encodedMsg)
    {
        // instantiated for case if return array is empty
        $returnArr = [];

        $wordsArr = explode(" ", $crib);
        $lastChunk = $wordsArr[sizeof($wordsArr) - 1];

        // get all words from dictionary which can be used for later guess
        $possibleWords = preg_grep("/\b" . $lastChunk . "\w*\b/", $this->dictionary);

        // return without any result to indeicate dead end
        if (! count($possibleWords)) {
            return $returnArr;
        }

        // reconstruct partials with guess
        foreach ($possibleWords as $word) {
            $wordsArr[sizeof($wordsArr) - 1] = $word;
            $extendedCrib = implode(" ", $wordsArr);

            // add space at the end if it's not already present
            // or does not has the exact length as the encoded one
            if (substr($extendedCrib, -1) != " " && strlen($encodedMsg) != strlen($extendedCrib)) {
                $extendedCrib = $extendedCrib . " ";
            }

            // only push to array if not overflowed
            if (strlen($encodedMsg) >= strlen($extendedCrib)) {
                array_push($returnArr, $extendedCrib);
            }
        }

        return $returnArr;
    }

    private function getNextEncodedMsg($encodedMsg)
    {
        $numOfObservableMsg = count($this->encryptedMsgArray);
        $indexOfCurrentlyObserved = array_search($encodedMsg, $this->encryptedMsgArray);
        $nextEncodedMsg = ($indexOfCurrentlyObserved + 1) % $numOfObservableMsg;

        return $this->encryptedMsgArray[$nextEncodedMsg];
    }

    private function recordKeyAsSolved($key) {

        $valid = true;
        foreach($this->encryptedMsgArray as $encodedMsg) {

            if (strlen($encodedMsg) > strlen($key)) {
                $valid = false;
                break;
            }

            $decodedMsg = $this->cryptographer->decrypt($encodedMsg, $this->lookupTable, $key);
            $words = explode(" ", $decodedMsg);
            foreach ($words as $word) {
                $valid = in_array($word, $this->dictionary) ? true : false;
                if($word == "ch") {
                    var_dump($word, $valid);
                }
            }
        }

        if ($valid && ! in_array($key, $this->keys)) {
            array_push($this->keys, $key);
        }
    }

}
