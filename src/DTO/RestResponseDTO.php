<?php
/**
 * Created by PhpStorm.
 * User: slimbook
 * Date: 17/10/18
 * Time: 20:59
 */

namespace MastermindAPI\DTO;


use MastermindAPI\Entity\Board;
use MastermindAPI\Entity\Guess;

class RestResponseDTO
{
    /** @var integer */
    private $code;

    /** @var string */
    private $error;

    /** @var Board|Guess|string */
    private $data;

    public function __construct($code, $error, $data)
    {
        $this->code = $code;
        $this->error = $error;
        $this->data = $data;
    }
}