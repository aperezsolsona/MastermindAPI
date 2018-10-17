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
     * Guess constructor.
     *
     * @param string $pegs
     * @param integer $blackPegs
     * @param integer $whitePegs
     * @param boolean $isCorrect
     * @param Board $board
     */
    public function __construct($pegs, $blackPegs, $whitePegs, $isCorrect, $board)
    {
        $this->pegs = $pegs;
        $this->blackPegs = $blackPegs;
        $this->whitePegs = $whitePegs;
        $this->isCorrect = $isCorrect;
        $this->board = $board;
    }

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
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getBlackPegs()
    {
        return $this->blackPegs;
    }

    /**
     * @return mixed
     */
    public function getWhitePegs()
    {
        return $this->whitePegs;
    }

    /**
     * @return mixed
     */
    public function getIsCorrect()
    {
        return $this->isCorrect;
    }

    /**
     * @return mixed
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * @ORM\PrePersist
     */
    public function updatedTimestamp()
    {
        $dateTimeNow = new \DateTime('now');
        if ($this->getCreatedAt() === null) {
            $this->createdAt = $dateTimeNow;
        }
    }

}
