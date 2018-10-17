<?php
/**
 * Board.php
 *
 * Board Entity
 *
 */

namespace MastermindAPI\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Board
 *
 * @ORM\Table(name="board")
 * @ORM\Entity(repositoryClass="MastermindAPI\Repository\BoardRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Board
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="code", type="json")
     */
    protected $code;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;


    /**
     * @ORM\OneToMany(targetEntity="MastermindAPI\Entity\Guess", mappedBy="board")
     */
    protected $guesses;


    public function __construct($code)
    {
        $this->code = $code;
        $this->guesses = new ArrayCollection();
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getGuesses()
    {
        return $this->guesses;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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
