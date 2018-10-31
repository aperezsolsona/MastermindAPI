<?php

namespace MastermindAPI\Service;

use MastermindAPI\DTO\GuessResultDTO;
use MastermindAPI\Exception\ValidationException;

class MastermindService
{

    const AVAILABLE_COLORS = array('R','O','Y','G','B','V');
    const NUMBER_OF_PEGS_IN_CODE = 4;


    /**
     * This function will return perfect matches and
     * modify the referenced array params so matched pegs will be emptied
     *
     * No array value finding functions have been used here for algorithmic clarity purposes
     *
     * @param $guessArray
     * @param $secretCodeArray
     * @return int
     *
     * @throws \Exception
     */
    private function getMatches(&$guessArray, &$secretCodeArray)
    {
        $blackMatches = 0;
        foreach ($secretCodeArray as $indexSecretCodeVal => $secretCodeVal) {
            if($secretCodeVal == $guessArray[$indexSecretCodeVal]) {
                $secretCodeArray[$indexSecretCodeVal] = $guessArray[$indexSecretCodeVal] = null;
                $blackMatches++;
            }
        }

        return $blackMatches;

    }

    /**
     * This function returns matches in different positions based in already iterated arrays
     *
     * No array value finding functions have been used here for algorithmic clarity purposes
     *
     * @param $guessArray
     * @param $secretCodeArray
     * @return int number of color matches
     */
    private function getImperfectMatches($guessArray, $secretCodeArray) {

        $whiteCount = 0;
        foreach ($guessArray as $indexGuessedVal => $guessedVal) {

            $currentPegGuess = $guessedVal;
            foreach ($secretCodeArray as $indexSecretCodeVal => $secretCodeVal) {

                if (!empty($secretCodeVal) && $currentPegGuess == $secretCodeVal) {
                    $guessArray[$indexGuessedVal] = null;
                    $currentPegGuess = null; //we dont want extra matches with this peg
                    $whiteCount++;
                }
            }
        }
        return $whiteCount;

    }

    /**
     * VALIDATES CORRECT LENGTH AND VALUES OF GUESS ARRAY
     * ( R , Y , O , B )
     *
     * @param $codeArray mixed Can be array or JSON
     * @return bool
     *
     * @throws ValidationException
     */
    public function validateCode($codeArray) {

        $error = '';

        if (is_string($codeArray)) {
            try {
                $codeArray = json_decode($codeArray);
            } catch (\Exception $e) {
                $error = 'Error validating code. JSON was malformed: ' . $codeArray . ':' . $error;
                throw new ValidationException($error);
            }
        }

        // validating array length
        if (count($codeArray) != self::NUMBER_OF_PEGS_IN_CODE ) {
            $error .= 'Incorrect number of pegs found, please send ' . self::NUMBER_OF_PEGS_IN_CODE . ' pegs. ';
        }

        //validating values in array
        $errorValues = array();
        foreach ($codeArray as $color) {
            if (empty($color)) {
                $errorValues[] = 'null';
            } elseif (!in_array($color, self::AVAILABLE_COLORS)) {
                $errorValues[] = $color;
            }
        }
        if (!empty($errorValues)) {
            $error .= 'There are incorrect value/s in the given set of pegs: {' .
                $this->printListWithCommas($errorValues) . '} is/are not valid colors. ';
        }


        if (!empty($error)) {
            $error = 'Error validating code {' . $this->printListWithCommas($codeArray) . '}: ' . $error;
            throw new ValidationException($error);
            //return false;
        }
        return true;
    }



    /**
     *
     * Outputs string with values from array. Converts nulls to -NULL- for better output
     *
     * @param $list
     * @return string
     */
    private function printListWithCommas($list) {

        if (!empty($list)) {
            $list = array_map(function($v){
                return (empty($v)) ? "-NULL-" : $v;
            }, $list);
            return rtrim(implode(', ', $list), ', ');
        }
        return '';
    }


    /**
     * Validates peg arrays and evaluates so it can return a Result DTO object with the response
     *
     * @param $guessArray
     * @param $secretCodeArray
     *
     * @return GuessResultDTO
     *
     * @throws ValidationException
     */
    public function evaluateGuess($guessArray, $secretCodeArray) {

        $result = null;
        try {
            $blackPegs = $this->getMatches($guessArray, $secretCodeArray);
            $whitePegs = $this->getImperfectMatches($guessArray, $secretCodeArray);

            $result = new GuessResultDTO();
            if ($blackPegs == self::NUMBER_OF_PEGS_IN_CODE) {
                $result->setCorrectGuess(true);
            }
            $result->setBlackPegs($blackPegs);
            $result->setWhitePegs($whitePegs);

            return $result;
        } catch (\Exception $e) {
            throw new ValidationException($e->getMessage());
        }

    }

    /**
     * Creates random mastermind code in array or json format
     *
     * @param bool $json Outputs in JSON
     * @return mixed
     */
    public function createRandomMastermindCode($json = false)
    {
        $randomCode = array();
        for ($i = 0; $i < self::NUMBER_OF_PEGS_IN_CODE; $i++) {
            $randIndex = array_rand(self::AVAILABLE_COLORS);
            $randomCode[$i] = self::AVAILABLE_COLORS[$randIndex];
        }
        if ($json) {
            $randomCode = json_encode($randomCode);
        }
        return $randomCode;
    }

}