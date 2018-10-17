<?php
/**
 *
 * This service encompasses logic for the REST API frontal
 *
 * Probably would be a good idea to merge RestService and MastermindService in a next iteration
 *
 * User: slimbook
 * Date: 17/10/18
 * Time: 15:49
 */

namespace MastermindAPI\Service;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use MastermindAPI\Entity\Board;
use MastermindAPI\Entity\Guess;

class RestService
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var MastermindService
     */
    protected $mastermind;

    /**
     * RestService constructor.
     *
     * Note the service dependency injection
     *
     * @param EntityManagerInterface $entityManager
     * @param MastermindService $mastermindService
     */
    public function __construct(EntityManagerInterface $entityManager, MastermindService $mastermindService)
    {
        $this->em = $entityManager;
        $this->mastermind = $mastermindService;
    }

    /**
     * @param string $codeJson
     * @return Board|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \MastermindAPI\Exception\ValidationException
     */
    public function addBoard($codeJson) {

        if (empty($codeJson)) {
            $codeJson = $this->mastermind->createRandomMastermindCode(true);
        }

        $board = null;
        if ($this->mastermind->validateCode($codeJson)) {
            $board = new Board($codeJson);
            $this->em->persist($board);
            $this->em->flush();
        }
        return $board;
    }

    /**
     * @param $boardid integer
     * @return Board|null
     */
    public function getBoard($boardid) {

        $board = null;
        try {
            if (!empty($boardid) && is_integer($boardid)) {
                /** @var Board $board */
                $board = $this->em->find('MastermindAPI\Entity\Board', $boardid);
            }
        } catch (\Exception $e) {
        }

        return $board;

    }

    /**
     * @param string $pegsCodeJson
     * @param integer $boardId
     * @return Guess|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \MastermindAPI\Exception\ValidationException
     */
    public function addGuess($pegsCodeJson, $boardId) {

        if ($this->mastermind->validateCode($pegsCodeJson)) {

            if (!empty($boardId)){
                /** @var Board $board */
                $board = $this->em->find('MastermindAPI\Entity\Board', $boardId);

                $guessResult = $this->mastermind->evaluateGuess(
                    json_decode($board->getCode()),
                    json_decode($pegsCodeJson)
                );

                $guess = new Guess(
                    $pegsCodeJson,
                    $guessResult->getBlackPegs(),
                    $guessResult->getWhitePegs(),
                    $guessResult->isCorrectGuess(),
                    $board
                );
                $this->em->persist($guess);
                $this->em->flush();
            }
        } else {
            $guess = null;
        }
        return $guess;
    }

}