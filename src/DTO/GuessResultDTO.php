<?php
/**
 * Created by PhpStorm.
 * User: slimbook
 * Date: 9/10/18
 * Time: 10:52
 */

namespace MastermindAPI\DTO;

class GuessResultDTO
{
    private $correctGuess = false;
    private $blackPegs = 0;
    private $whitePegs = 0;

    /**
     * @return bool
     */
    public function isCorrectGuess()
    {
        return $this->correctGuess;
    }

    /**
     * @param bool $correctGuess
     */
    public function setCorrectGuess($correctGuess)
    {
        $this->correctGuess = $correctGuess;
    }

    /**
     * @return int
     */
    public function getBlackPegs()
    {
        return $this->blackPegs;
    }

    /**
     * @param int $blackPegs
     */
    public function setBlackPegs($blackPegs)
    {
        $this->blackPegs = $blackPegs;
    }

    /**
     * @return int
     */
    public function getWhitePegs()
    {
        return $this->whitePegs;
    }

    /**
     * @param int $whitePegs
     */
    public function setWhitePegs($whitePegs)
    {
        $this->whitePegs = $whitePegs;
    }
}