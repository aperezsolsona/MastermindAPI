<?php
/**
 * Guess.php
 *
 * Guess Entity
 */

namespace MastermindAPI\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Guess
 * @ORM\Table(name="guess")
 * @ORM\Entity(repositoryClass="MastermindAPI\Repository\GuessRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Guess
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="pegs", type="json")
     */
    protected $pegs;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="black_pegs", type="integer", options={"default":0})
     */
    protected $blackPegs;

    /**
     * @ORM\Column(name="white_pegs", type="integer", options={"default":0})
     */
    protected $whitePegs;

    /**
     * @ORM\Column(name="is_correct", type="boolean", options={"default":false})
     */
    protected $isCorrect;

    /**
     * @ORM\ManyToOne(targetEntity="MastermindAPI\Entity\Board", inversedBy="guesses")
     * @ORM\JoinColumn(name="board_id", referencedColumnName="id")
     * @Serializer\Exclude()
     *
     */
    protected $board;



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPegs()
    {
        return $this->pegs;
    }

    /**
     * @param mixed $pegs
     */
    public function setPegs($pegs): void
    {
        $this->pegs = $pegs;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getBlackPegs()
    {
        return $this->blackPegs;
    }

    /**
     * @param mixed $blackPegs
     */
    public function setBlackPegs($blackPegs): void
    {
        $this->blackPegs = $blackPegs;
    }

    /**
     * @return mixed
     */
    public function getWhitePegs()
    {
        return $this->whitePegs;
    }

    /**
     * @param mixed $whitePegs
     */
    public function setWhitePegs($whitePegs): void
    {
        $this->whitePegs = $whitePegs;
    }

    /**
     * @return mixed
     */
    public function getisCorrect()
    {
        return $this->isCorrect;
    }

    /**
     * @param mixed $isCorrect
     */
    public function setIsCorrect($isCorrect): void
    {
        $this->isCorrect = $isCorrect;
    }

    /**
     * @return mixed
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * @param mixed $board
     */
    public function setBoard($board)
    {
        $this->board = $board;
    }

    /**
     * @ORM\PrePersist
     */
    public function updatedTimestamp()
    {
        $dateTimeNow = new \DateTime('now');
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($dateTimeNow);
        }
    }

}
