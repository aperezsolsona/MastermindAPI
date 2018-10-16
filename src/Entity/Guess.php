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
